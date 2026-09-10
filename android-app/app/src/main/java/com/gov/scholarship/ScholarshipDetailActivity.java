package com.gov.scholarship;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.api.SessionManager;
import org.json.JSONObject;

public class ScholarshipDetailActivity extends AppCompatActivity {

    private SessionManager sessionManager;
    private int scholarshipId = 1;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_scholarship_detail);

        sessionManager = new SessionManager(this);

        TextView txtTitle = findViewById(R.id.txtDetTitle);
        TextView txtCategory = findViewById(R.id.txtDetCategory);
        TextView txtDesc = findViewById(R.id.txtDetDesc);
        TextView txtElig = findViewById(R.id.txtDetElig);
        Button btnApply = findViewById(R.id.btnDetApply);

        // Fetch intent extras passed from adapter
        String idStr = getIntent().getStringExtra("SCHOLARSHIP_ID");
        String name = getIntent().getStringExtra("SCHOLARSHIP_NAME");
        String category = getIntent().getStringExtra("SCHOLARSHIP_CATEGORY");

        if (idStr != null) {
            try {
                scholarshipId = Integer.parseInt(idStr);
            } catch (Exception ignored) { }
        }

        if (name != null) txtTitle.setText(name);
        if (category != null) txtCategory.setText(category);

        // Fetch latest scheme details from API
        ApiClient.get("scholarship_detail.php?id=" + scholarshipId, new ApiClient.ApiCallback() {
            @Override
            public void onSuccess(JSONObject response) {
                JSONObject data = response.optJSONObject("data");
                if (data != null) {
                    txtTitle.setText(data.optString("name", name));
                    txtCategory.setText(getString(R.string.label_sector, data.optString("category", category)));
                    String desc = data.optString("description", "");
                    if (!desc.isEmpty()) txtDesc.setText(desc);
                    String elig = data.optString("eligibility", "");
                    if (!elig.isEmpty()) txtElig.setText(elig);
                }
            }

            @Override
            public void onError(String errorMessage) {
                // Keep intent defaults
            }
        });

        btnApply.setOnClickListener(v -> {
            if (!sessionManager.isLoggedIn()) {
                Toast.makeText(this, R.string.msg_login_required, Toast.LENGTH_LONG).show();
                Intent loginIntent = new Intent(ScholarshipDetailActivity.this, LoginActivity.class);
                startActivity(loginIntent);
                return;
            }

            btnApply.setEnabled(false);
            btnApply.setText(R.string.msg_submitting_app);

            try {
                JSONObject payload = new JSONObject();
                payload.put("student_id", sessionManager.getStudentId());
                payload.put("scholarship_id", scholarshipId);

                ApiClient.post("apply.php", payload, new ApiClient.ApiCallback() {
                    @Override
                    public void onSuccess(JSONObject response) {
                        btnApply.setEnabled(true);
                        btnApply.setText(R.string.btn_apply);

                        String refId = "NSP-APP-" + scholarshipId;
                        JSONObject data = response.optJSONObject("data");
                        if (data != null && data.has("reference_id")) {
                            refId = data.optString("reference_id");
                        }

                        Toast.makeText(ScholarshipDetailActivity.this, 
                                getString(R.string.msg_app_submitted, refId), 
                                Toast.LENGTH_LONG).show();
                        finish();
                    }

                    @Override
                    public void onError(String errorMessage) {
                        btnApply.setEnabled(true);
                        btnApply.setText(R.string.btn_apply);
                        Toast.makeText(ScholarshipDetailActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });

            } catch (Exception e) {
                btnApply.setEnabled(true);
                btnApply.setText(R.string.btn_apply);
                Toast.makeText(this, "Error: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });
    }
}
