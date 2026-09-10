package com.gov.scholarship;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Button;
import androidx.appcompat.app.AppCompatActivity;

public class OnboardingActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_onboarding);

        Button btnNext = findViewById(R.id.btnNext);
        Button btnSkip = findViewById(R.id.btnSkip);

        // Transition triggers for mock flow
        btnNext.setOnClickListener(v -> navigateToLogin());
        btnSkip.setOnClickListener(v -> navigateToLogin());
    }

    private void navigateToLogin() {
        Intent intent = new Intent(OnboardingActivity.this, LoginActivity.class);
        startActivity(intent);
        finish();
    }
}
