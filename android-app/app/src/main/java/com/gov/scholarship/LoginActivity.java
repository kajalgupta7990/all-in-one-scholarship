package com.gov.scholarship;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.api.SessionManager;
import org.json.JSONObject;

public class LoginActivity extends AppCompatActivity {

    private SessionManager sessionManager;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        sessionManager = new SessionManager(this);

        EditText etEmail = findViewById(R.id.etEmail);
        EditText etPassword = findViewById(R.id.etPassword);
        Button btnLogin = findViewById(R.id.btnLogin);
        TextView txtRegisterRedirect = findViewById(R.id.txtRegisterRedirect);

        btnLogin.setOnClickListener(v -> {
            String email = etEmail.getText().toString().trim();
            String password = etPassword.getText().toString().trim();

            if (email.isEmpty() || password.isEmpty()) {
                Toast.makeText(this, getString(R.string.msg_fill_all), Toast.LENGTH_SHORT).show();
                return;
            }

            btnLogin.setEnabled(false);
            btnLogin.setText(R.string.msg_authenticating);

            try {
                JSONObject payload = new JSONObject();
                payload.put("email", email);
                payload.put("password", password);

                ApiClient.post("login.php", payload, new ApiClient.ApiCallback() {
                    @Override
                    public void onSuccess(JSONObject response) {
                        btnLogin.setEnabled(true);
                        btnLogin.setText(R.string.btn_login);

                        JSONObject data = response.optJSONObject("data");
                        if (data != null) {
                            int id = data.optInt("student_id", 1);
                            String name = data.optString("full_name", "Student");
                            String emailStr = data.optString("email", email);
                            String phone = data.optString("phone", "");
                            String course = data.optString("course", "General");
                            String category = data.optString("category", "General");
                            String status = data.optString("verification_status", "Pending");

                            sessionManager.saveStudent(id, name, emailStr, phone, course, category, status);
                            Toast.makeText(LoginActivity.this, getString(R.string.msg_login_success, name), Toast.LENGTH_LONG).show();

                            Intent intent = new Intent(LoginActivity.this, MainActivity.class);
                            startActivity(intent);
                            finish();
                        } else {
                            Intent intent = new Intent(LoginActivity.this, MainActivity.class);
                            startActivity(intent);
                            finish();
                        }
                    }

                    @Override
                    public void onError(String errorMessage) {
                        btnLogin.setEnabled(true);
                        btnLogin.setText(R.string.btn_login);
                        Toast.makeText(LoginActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });

            } catch (Exception e) {
                btnLogin.setEnabled(true);
                btnLogin.setText("Authenticate Credentials");
                Toast.makeText(this, "Error building request: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });

        txtRegisterRedirect.setOnClickListener(v -> {
            Intent intent = new Intent(LoginActivity.this, RegisterActivity.class);
            startActivity(intent);
        });
    }
}
