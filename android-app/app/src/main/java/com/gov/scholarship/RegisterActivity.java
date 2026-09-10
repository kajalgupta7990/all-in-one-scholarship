package com.gov.scholarship;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.gov.scholarship.api.ApiClient;
import org.json.JSONObject;

public class RegisterActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_register);

        EditText etName = findViewById(R.id.etRegName);
        EditText etEmail = findViewById(R.id.etRegEmail);
        EditText etPhone = findViewById(R.id.etRegPhone);
        EditText etCourse = findViewById(R.id.etRegCourse);
        EditText etCategory = findViewById(R.id.etRegCategory);
        EditText etAddress = findViewById(R.id.etRegAddress);
        EditText etPass = findViewById(R.id.etRegPass);
        EditText etConfirm = findViewById(R.id.etRegConfirm);
        Button btnRegister = findViewById(R.id.btnRegister);
        TextView txtLoginRedirect = findViewById(R.id.txtLoginRedirect);

        btnRegister.setOnClickListener(v -> {
            String name = etName.getText().toString().trim();
            String email = etEmail.getText().toString().trim();
            String phone = etPhone.getText().toString().trim();
            String course = etCourse != null ? etCourse.getText().toString().trim() : "B.Tech";
            String category = etCategory != null ? etCategory.getText().toString().trim() : "General";
            String address = etAddress != null ? etAddress.getText().toString().trim() : "Address";
            String pass = etPass != null ? etPass.getText().toString().trim() : "";
            String confirm = etConfirm != null ? etConfirm.getText().toString().trim() : "";

            if (name.isEmpty() || email.isEmpty() || phone.isEmpty() || pass.isEmpty()) {
                Toast.makeText(this, "Please fill in all mandatory fields including password", Toast.LENGTH_SHORT).show();
                return;
            }

            if (!pass.equals(confirm)) {
                Toast.makeText(this, "Passwords do not match", Toast.LENGTH_SHORT).show();
                return;
            }

            btnRegister.setEnabled(false);
            btnRegister.setText("Registering Profile...");

            try {
                JSONObject payload = new JSONObject();
                payload.put("full_name", name);
                payload.put("email", email);
                payload.put("phone", phone);
                payload.put("course", !course.isEmpty() ? course : "B.Tech Computer Science");
                payload.put("category", !category.isEmpty() ? category : "General");
                payload.put("address", !address.isEmpty() ? address : "Registered Address");
                payload.put("password", pass);

                ApiClient.post("register.php", payload, new ApiClient.ApiCallback() {
                    @Override
                    public void onSuccess(JSONObject response) {
                        btnRegister.setEnabled(true);
                        btnRegister.setText("Register Profile");
                        Toast.makeText(RegisterActivity.this, "Registration Successful! Please login with your credentials.", Toast.LENGTH_LONG).show();

                        Intent intent = new Intent(RegisterActivity.this, LoginActivity.class);
                        startActivity(intent);
                        finish();
                    }

                    @Override
                    public void onError(String errorMessage) {
                        btnRegister.setEnabled(true);
                        btnRegister.setText("Register Profile");
                        Toast.makeText(RegisterActivity.this, errorMessage, Toast.LENGTH_LONG).show();
                    }
                });

            } catch (Exception e) {
                btnRegister.setEnabled(true);
                btnRegister.setText("Register Profile");
                Toast.makeText(this, "Error building request: " + e.getMessage(), Toast.LENGTH_SHORT).show();
            }
        });

        txtLoginRedirect.setOnClickListener(v -> {
            Intent intent = new Intent(RegisterActivity.this, LoginActivity.class);
            startActivity(intent);
            finish();
        });
    }
}
