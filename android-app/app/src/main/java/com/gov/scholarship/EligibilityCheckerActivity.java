package com.gov.scholarship;

import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.cardview.widget.CardView;

public class EligibilityCheckerActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_eligibility_checker);

        EditText etIncome = findViewById(R.id.etChkIncome);
        EditText etMarks = findViewById(R.id.etChkMarks);
        Button btnCheck = findViewById(R.id.btnCheck);
        CardView cardResult = findViewById(R.id.cardCheckResult);
        TextView txtResultBody = findViewById(R.id.txtResultBody);

        btnCheck.setOnClickListener(v -> {
            String incomeStr = etIncome.getText().toString().trim();
            String marksStr = etMarks.getText().toString().trim();

            if (incomeStr.isEmpty() || marksStr.isEmpty()) {
                Toast.makeText(this, "Please enter all fields", Toast.LENGTH_SHORT).show();
                return;
            }

            double income = Double.parseDouble(incomeStr);
            double marks = Double.parseDouble(marksStr);

            if (marks >= 75 && income <= 4000) {
                txtResultBody.setText("Congratulations! You qualify for:\n• Central Sector Scheme of Scholarship\n• PG Scholarship for Single Girl Child");
                cardResult.setVisibility(View.VISIBLE);
            } else if (income <= 2000) {
                txtResultBody.setText("Congratulations! You qualify for:\n• Post-Matric Scholarship Scheme for SC/ST");
                cardResult.setVisibility(View.VISIBLE);
            } else {
                txtResultBody.setText("No direct matches found. Try entering alternative income/marks profiles or browse schemes directory.");
                cardResult.setVisibility(View.VISIBLE);
            }
        });
    }
}
