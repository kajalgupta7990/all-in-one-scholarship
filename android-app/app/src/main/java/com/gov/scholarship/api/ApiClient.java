package com.gov.scholarship.api;

import android.os.Handler;
import android.os.Looper;
import org.json.JSONObject;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.nio.charset.StandardCharsets;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class ApiClient {

    /**
     * BASE_URL for PHP REST API
     * For Android Emulator: use "http://10.0.2.2/2412061/project/student-portal/api/"
     * For Physical Device: replace 10.0.2.2 with your computer's local Wi-Fi IP (e.g. "http://192.168.1.100/...")
     */
    public static final String BASE_URL = "http://10.0.2.2/2412061/project/student-portal/api/";

    private static final ExecutorService executor = Executors.newFixedThreadPool(4);
    private static final Handler mainHandler = new Handler(Looper.getMainLooper());

    public interface ApiCallback {
        void onSuccess(JSONObject response);
        void onError(String errorMessage);
    }

    /**
     * Perform HTTP GET request on background thread
     */
    public static void get(String endpoint, ApiCallback callback) {
        executor.execute(() -> {
            HttpURLConnection conn = null;
            try {
                String fullUrl = endpoint.startsWith("http") ? endpoint : BASE_URL + endpoint;
                URL url = new URL(fullUrl);
                conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("GET");
                conn.setRequestProperty("Accept", "application/json");
                conn.setConnectTimeout(8000);
                conn.setReadTimeout(8000);

                int responseCode = conn.getResponseCode();
                InputStream is = (responseCode >= 200 && responseCode < 400) 
                        ? conn.getInputStream() 
                        : conn.getErrorStream();

                String responseText = readStream(is);
                JSONObject json = new JSONObject(responseText);

                mainHandler.post(() -> {
                    if (callback != null) {
                        if (json.optBoolean("success", false)) {
                            callback.onSuccess(json);
                        } else {
                            callback.onError(json.optString("message", "Request failed"));
                        }
                    }
                });

            } catch (Exception e) {
                mainHandler.post(() -> {
                    if (callback != null) {
                        callback.onError("Connection error: " + e.getLocalizedMessage());
                    }
                });
            } finally {
                if (conn != null) conn.disconnect();
            }
        });
    }

    /**
     * Perform HTTP POST request with JSON payload on background thread
     */
    public static void post(String endpoint, JSONObject postData, ApiCallback callback) {
        executor.execute(() -> {
            HttpURLConnection conn = null;
            try {
                String fullUrl = endpoint.startsWith("http") ? endpoint : BASE_URL + endpoint;
                URL url = new URL(fullUrl);
                conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/json; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);
                conn.setConnectTimeout(8000);
                conn.setReadTimeout(8000);

                if (postData != null) {
                    try (OutputStream os = conn.getOutputStream()) {
                        byte[] input = postData.toString().getBytes(StandardCharsets.UTF_8);
                        os.write(input, 0, input.length);
                    }
                }

                int responseCode = conn.getResponseCode();
                InputStream is = (responseCode >= 200 && responseCode < 400) 
                        ? conn.getInputStream() 
                        : conn.getErrorStream();

                String responseText = readStream(is);
                JSONObject json = new JSONObject(responseText);

                mainHandler.post(() -> {
                    if (callback != null) {
                        if (json.optBoolean("success", false)) {
                            callback.onSuccess(json);
                        } else {
                            callback.onError(json.optString("message", "Request failed"));
                        }
                    }
                });

            } catch (Exception e) {
                mainHandler.post(() -> {
                    if (callback != null) {
                        callback.onError("Connection error: " + e.getLocalizedMessage());
                    }
                });
            } finally {
                if (conn != null) conn.disconnect();
            }
        });
    }

    private static String readStream(InputStream is) throws Exception {
        if (is == null) return "{}";
        BufferedReader reader = new BufferedReader(new InputStreamReader(is, StandardCharsets.UTF_8));
        StringBuilder sb = new StringBuilder();
        String line;
        while ((line = reader.readLine()) != null) {
            sb.append(line);
        }
        reader.close();
        return sb.toString();
    }
}
