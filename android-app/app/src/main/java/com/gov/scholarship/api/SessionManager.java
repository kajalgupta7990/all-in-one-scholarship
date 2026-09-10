package com.gov.scholarship.api;

import android.content.Context;
import android.content.SharedPreferences;

public class SessionManager {

    private static final String PREF_NAME = "GovScholarshipPref";
    private static final String KEY_IS_LOGGED_IN = "is_logged_in";
    private static final String KEY_STUDENT_ID = "student_id";
    private static final String KEY_NAME = "full_name";
    private static final String KEY_EMAIL = "email";
    private static final String KEY_PHONE = "phone";
    private static final String KEY_COURSE = "course";
    private static final String KEY_CATEGORY = "category";
    private static final String KEY_STATUS = "verification_status";

    private SharedPreferences pref;
    private SharedPreferences.Editor editor;
    private Context context;

    public SessionManager(Context context) {
        this.context = context;
        pref = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = pref.edit();
    }

    public void saveStudent(int id, String name, String email, String phone, String course, String category, String status) {
        editor.putBoolean(KEY_IS_LOGGED_IN, true);
        editor.putInt(KEY_STUDENT_ID, id);
        editor.putString(KEY_NAME, name);
        editor.putString(KEY_EMAIL, email);
        editor.putString(KEY_PHONE, phone);
        editor.putString(KEY_COURSE, course);
        editor.putString(KEY_CATEGORY, category);
        editor.putString(KEY_STATUS, status);
        editor.apply();
    }

    public boolean isLoggedIn() {
        return pref.getBoolean(KEY_IS_LOGGED_IN, false);
    }

    public int getStudentId() {
        return pref.getInt(KEY_STUDENT_ID, 1);
    }

    public String getStudentName() {
        return pref.getString(KEY_NAME, "Student Applicant");
    }

    public String getStudentEmail() {
        return pref.getString(KEY_EMAIL, "student@email.com");
    }

    public String getStudentPhone() {
        return pref.getString(KEY_PHONE, "9876543210");
    }

    public String getStudentCourse() {
        return pref.getString(KEY_COURSE, "B.Tech Computer Science");
    }

    public String getStudentCategory() {
        return pref.getString(KEY_CATEGORY, "General");
    }

    public String getVerificationStatus() {
        return pref.getString(KEY_STATUS, "Pending");
    }

    public void logout() {
        editor.clear();
        editor.apply();
    }
}
