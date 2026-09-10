package com.gov.scholarship;

import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.api.SessionManager;
import org.json.JSONObject;

public class HelpSupportActivity extends AppCompatActivity {

    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_help_support);

        sessionManager = new SessionManager(this);

        EditText etName = findViewById(R.id.etHelpName);
        EditText etQuery = findViewById(R.id.etHelpQuery);
        Button btnSubmit = findViewById(R.id.btnSubmitHelp);

        if (sessionManager.isLoggedIn()) {
            etName.setText(sessionManager.getStudentName());
        }

        btnSubmit.setOnClickListener(v -> {
            String name = etName.getText().toString().trim();
            String query = etQuery.getText().toString().trim();

            if (name.isEmpty() || query.isEmpty()) {
                Toast.makeText(this, getString(R.string.msg_fill_all), Toast.LENGTH_SHORT).show();
                return;
            }

            btnSubmit.setEnabled(false);
            btnSubmit.setText(R.string.msg_submitting_ticket);

            try {
                JSONObject payload = new JSONObject();
                payload.put("name", name);
                payload.put("message", query);
                payload.put("subject", "Help & Support Desk Inquiry");
                if (sessionManager.isLoggedIn()) {
                    payload.put("student_id", sessionManager.getStudentId());
                    payload.put("email", sessionManager.getStudentEmail());
                    payload.put("phone", sessionManager.getStudentPhone());
                }

                ApiClient.post("contact.php", payload, new ApiClient.ApiCallback() {
                    @Override
                    public void onSuccess(JSONObject response) {
                        btnSubmit.setEnabled(true);
                        btnSubmit.setText(R.string.btn_submit_help);

                        String ticketId = "NSP-MOB-TICKET";
                        JSONObject data = response.optJSONObject("data");
                        if (data != null && data.has("ticket_id")) {
                            ticketId = data.optString("ticket_id");
                        }

                        Toast.makeText(HelpSupportActivity.this, 
                                getString(R.string.msg_ticket_submitted, ticketId), 
                                Toast.LENGTH_LONG).show();
                        finish();
                    }

                    @Override
                    public void onError(String errorMessage) {
                        btnSubmit.setEnabled(true);
                        btnSubmit.setText(R.string.btn_submit_help);
                        Toast.makeText(HelpSupportActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });

            } catch (Exception e) {
                btnSubmit.setEnabled(true);
                btnSubmit.setText("Submit Support Query");
                Toast.makeText(this, "Error: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}
