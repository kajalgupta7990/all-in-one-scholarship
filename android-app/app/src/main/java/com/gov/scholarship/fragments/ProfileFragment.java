package com.gov.scholarship.fragments;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.FrameLayout;
import android.widget.TextView;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import com.gov.scholarship.LoginActivity;
import com.gov.scholarship.R;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.api.SessionManager;
import org.json.JSONObject;

public class ProfileFragment extends Fragment {

    private SessionManager sessionManager;

    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_profile, container, false);

        sessionManager = new SessionManager(requireContext());

        TextView txtName = view.findViewById(R.id.txtName);
        TextView txtEmail = view.findViewById(R.id.txtEmail);
        TextView chipAadhaar = view.findViewById(R.id.chipAadhaar);
        TextView txtAvatarInitials = view.findViewById(R.id.txtAvatarInitials);
        TextView txtProfileCourse = view.findViewById(R.id.txtProfileCourse);
        TextView txtProfileCategory = view.findViewById(R.id.txtProfileCategory);
        TextView txtProfileBank = view.findViewById(R.id.txtProfileBank);
        TextView txtProfileAccount = view.findViewById(R.id.txtProfileAccount);
        android.widget.Button btnProfileLogout = view.findViewById(R.id.btnProfileLogout);

        // Load local session data first
        if (sessionManager.isLoggedIn()) {
            String initialName = sessionManager.getStudentName();
            txtName.setText(initialName);
            txtEmail.setText(sessionManager.getStudentEmail());
            chipAadhaar.setText(sessionManager.getVerificationStatus().equals("Verified") ? "Verified by Nodal Office" : "Verification Pending");
            txtProfileCourse.setText(sessionManager.getStudentCourse().isEmpty() ? "Not specified" : sessionManager.getStudentCourse());
            txtProfileCategory.setText(sessionManager.getStudentCategory().isEmpty() ? "General" : sessionManager.getStudentCategory());

            if (initialName != null && !initialName.trim().isEmpty()) {
                String[] parts = initialName.trim().split("\\s+");
                String initials = parts.length > 1
                        ? ("" + parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
                        : ("" + parts[0].charAt(0)).toUpperCase();
                txtAvatarInitials.setText(initials);
            }

            btnProfileLogout.setText(R.string.btn_sign_out);
            btnProfileLogout.setOnClickListener(v -> {
                sessionManager.logout();
                Intent loginIntent = new Intent(getActivity(), LoginActivity.class);
                loginIntent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                startActivity(loginIntent);
                if (getActivity() != null) getActivity().finish();
            });

            // Fetch latest real-time profile from MySQL database via API
            ApiClient.get("profile.php?student_id=" + sessionManager.getStudentId(), new ApiClient.ApiCallback() {
                @Override
                public void onSuccess(JSONObject response) {
                    JSONObject data = response.optJSONObject("data");
                    if (data != null && isAdded()) {
                        String name = data.optString("full_name", sessionManager.getStudentName());
                        String email = data.optString("email", sessionManager.getStudentEmail());
                        String status = data.optString("verification_status", "Pending");
                        String course = data.optString("course", sessionManager.getStudentCourse());
                        String category = data.optString("category", sessionManager.getStudentCategory());
                        String bank = data.optString("bank_name", "State Bank of India");
                        String acc = data.optString("account_number", "");

                        txtName.setText(name);
                        txtEmail.setText(email);
                        chipAadhaar.setText(status.equals("Verified") ? "Verified by Nodal Office" : "Verification " + status);
                        txtProfileCourse.setText(course);
                        txtProfileCategory.setText(category);
                        txtProfileBank.setText(!bank.isEmpty() ? bank : "State Bank of India");
                        txtProfileAccount.setText(!acc.isEmpty() ? "••••••••" + (acc.length() > 4 ? acc.substring(acc.length() - 4) : acc) : "••••••••364");

                        if (name != null && !name.trim().isEmpty()) {
                            String[] parts = name.trim().split("\\s+");
                            String initials = parts.length > 1
                                    ? ("" + parts[0].charAt(0) + parts[parts.length - 1].charAt(0)).toUpperCase()
                                    : ("" + parts[0].charAt(0)).toUpperCase();
                            txtAvatarInitials.setText(initials);
                        }

                        sessionManager.saveStudent(sessionManager.getStudentId(), name, email, 
                                data.optString("phone"), course, category, status);
                    }
                }

                @Override
                public void onError(String errorMessage) {
                    // Fall back to saved session
                }
            });
        } else {
            txtName.setText("Guest Student");
            txtEmail.setText("Not logged in");
            chipAadhaar.setText("Tap to Login");
            chipAadhaar.setOnClickListener(v -> {
                startActivity(new Intent(getActivity(), LoginActivity.class));
            });
            btnProfileLogout.setText("Log In");
            btnProfileLogout.setOnClickListener(v -> {
                startActivity(new Intent(getActivity(), LoginActivity.class));
            });
        }

        return view;
    }
}
