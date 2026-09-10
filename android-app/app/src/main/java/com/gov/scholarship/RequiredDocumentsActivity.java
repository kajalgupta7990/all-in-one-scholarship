package com.gov.scholarship;

import android.content.Intent;
import android.database.Cursor;
import android.net.Uri;
import android.os.Bundle;
import android.provider.OpenableColumns;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.gov.scholarship.api.ApiClient;
import com.gov.scholarship.api.SessionManager;

import org.json.JSONObject;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.concurrent.ExecutorService;
import java.util.concurrent.Executors;

public class RequiredDocumentsActivity extends AppCompatActivity {

    private static final int PICK_AADHAAR = 1001;
    private static final int PICK_INCOME = 1002;
    private static final int PICK_BONAFIDE = 1003;
    private static final int PICK_CASTE = 1004;
    private static final int PICK_DOMICILE = 1005;
    private static final int PICK_BANK = 1006;
    private static final int PICK_MARKSHEET = 1007;

    private static final long MAX_FILE_SIZE = 2 * 1024 * 1024;

    private Uri aadhaarUri;
    private Uri incomeUri;
    private Uri bonafideUri;
    private Uri casteUri;
    private Uri domicileUri;
    private Uri bankUri;
    private Uri marksheetUri;

    private TextView tvAadhaarFileName;
    private TextView tvIncomeFileName;
    private TextView tvBonafideFileName;
    private TextView tvCasteFileName;
    private TextView tvDomicileFileName;
    private TextView tvBankFileName;
    private TextView tvMarksheetFileName;

    private Button btnUploadAadhaar;
    private Button btnUploadIncome;
    private Button btnUploadBonafide;
    private Button btnUploadCaste;
    private Button btnUploadDomicile;
    private Button btnUploadBank;
    private Button btnUploadMarksheet;

    private SessionManager sessionManager;

    private final ExecutorService executorService =
            Executors.newSingleThreadExecutor();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_required_documents);

        sessionManager = new SessionManager(this);

        initializeViews();
        setupClickListeners();
    }

    private void initializeViews() {

        tvAadhaarFileName = findViewById(R.id.tvAadhaarFileName);
        tvIncomeFileName = findViewById(R.id.tvIncomeFileName);
        tvBonafideFileName = findViewById(R.id.tvBonafideFileName);
        tvCasteFileName = findViewById(R.id.tvCasteFileName);
        tvDomicileFileName = findViewById(R.id.tvDomicileFileName);
        tvBankFileName = findViewById(R.id.tvBankFileName);
        tvMarksheetFileName = findViewById(R.id.tvMarksheetFileName);

        btnUploadAadhaar = findViewById(R.id.btnUploadAadhaar);
        btnUploadIncome = findViewById(R.id.btnUploadIncome);
        btnUploadBonafide = findViewById(R.id.btnUploadBonafide);
        btnUploadCaste = findViewById(R.id.btnUploadCaste);
        btnUploadDomicile = findViewById(R.id.btnUploadDomicile);
        btnUploadBank = findViewById(R.id.btnUploadBank);
        btnUploadMarksheet = findViewById(R.id.btnUploadMarksheet);

        btnUploadAadhaar.setEnabled(false);
        btnUploadIncome.setEnabled(false);
        btnUploadBonafide.setEnabled(false);
        btnUploadCaste.setEnabled(false);
        btnUploadDomicile.setEnabled(false);
        btnUploadBank.setEnabled(false);
        btnUploadMarksheet.setEnabled(false);
    }

    private void setupClickListeners() {

        findViewById(R.id.btnSelectAadhaar).setOnClickListener(v ->
                openFilePicker(PICK_AADHAAR));

        findViewById(R.id.btnSelectIncome).setOnClickListener(v ->
                openFilePicker(PICK_INCOME));

        findViewById(R.id.btnSelectBonafide).setOnClickListener(v ->
                openFilePicker(PICK_BONAFIDE));

        findViewById(R.id.btnSelectCaste).setOnClickListener(v ->
                openFilePicker(PICK_CASTE));

        findViewById(R.id.btnSelectDomicile).setOnClickListener(v ->
                openFilePicker(PICK_DOMICILE));

        findViewById(R.id.btnSelectBank).setOnClickListener(v ->
                openFilePicker(PICK_BANK));

        findViewById(R.id.btnSelectMarksheet).setOnClickListener(v ->
                openFilePicker(PICK_MARKSHEET));


        btnUploadAadhaar.setOnClickListener(v ->
                uploadSelectedDocument(aadhaarUri, "Aadhaar Card"));

        btnUploadIncome.setOnClickListener(v ->
                uploadSelectedDocument(incomeUri, "Income Certificate"));

        btnUploadBonafide.setOnClickListener(v ->
                uploadSelectedDocument(bonafideUri, "Bonafide Certificate"));

        btnUploadCaste.setOnClickListener(v ->
                uploadSelectedDocument(casteUri, "Caste Certificate"));

        btnUploadDomicile.setOnClickListener(v ->
                uploadSelectedDocument(domicileUri, "Domicile Certificate"));

        btnUploadBank.setOnClickListener(v ->
                uploadSelectedDocument(bankUri, "Bank Passbook Copy"));

        btnUploadMarksheet.setOnClickListener(v ->
                uploadSelectedDocument(marksheetUri, "Previous Year Marksheet"));
    }

    private void openFilePicker(int requestCode) {
    Intent intent = new Intent(Intent.ACTION_OPEN_DOCUMENT);
    intent.addCategory(Intent.CATEGORY_OPENABLE);
    intent.setType("*/*");

    startActivityForResult(intent, requestCode);
}

    @Override
    protected void onActivityResult(
            int requestCode,
            int resultCode,
            Intent data) {

        super.onActivityResult(requestCode, resultCode, data);

        if (resultCode != RESULT_OK || data == null || data.getData() == null) {
            return;
        }

        Uri selectedUri = data.getData();

        long fileSize = getFileSize(selectedUri);

        if (fileSize > MAX_FILE_SIZE) {

            Toast.makeText(
                    this,
                    "File size must be 2 MB or less.",
                    Toast.LENGTH_LONG
            ).show();

            return;
        }

        String fileName = getFileName(selectedUri);

        if (!isAllowedFile(fileName)) {

            Toast.makeText(
                    this,
                    "Only PDF, JPG, JPEG and PNG files are allowed.",
                    Toast.LENGTH_LONG
            ).show();

            return;
        }

        switch (requestCode) {

            case PICK_AADHAAR:
                aadhaarUri = selectedUri;
                tvAadhaarFileName.setText(fileName);
                btnUploadAadhaar.setEnabled(true);
                break;

            case PICK_INCOME:
                incomeUri = selectedUri;
                tvIncomeFileName.setText(fileName);
                btnUploadIncome.setEnabled(true);
                break;

            case PICK_BONAFIDE:
                bonafideUri = selectedUri;
                tvBonafideFileName.setText(fileName);
                btnUploadBonafide.setEnabled(true);
                break;

            case PICK_CASTE:
                casteUri = selectedUri;
                tvCasteFileName.setText(fileName);
                btnUploadCaste.setEnabled(true);
                break;

            case PICK_DOMICILE:
                domicileUri = selectedUri;
                tvDomicileFileName.setText(fileName);
                btnUploadDomicile.setEnabled(true);
                break;

            case PICK_BANK:
                bankUri = selectedUri;
                tvBankFileName.setText(fileName);
                btnUploadBank.setEnabled(true);
                break;

            case PICK_MARKSHEET:
                marksheetUri = selectedUri;
                tvMarksheetFileName.setText(fileName);
                btnUploadMarksheet.setEnabled(true);
                break;
        }
    }

    private boolean isAllowedFile(String fileName) {

        if (fileName == null) {
            return false;
        }

        String lowerName = fileName.toLowerCase();

        return lowerName.endsWith(".pdf")
                || lowerName.endsWith(".jpg")
                || lowerName.endsWith(".jpeg")
                || lowerName.endsWith(".png");
    }

    private long getFileSize(Uri uri) {

        Cursor cursor = null;

        try {

            cursor = getContentResolver().query(
                    uri,
                    null,
                    null,
                    null,
                    null
            );

            if (cursor != null && cursor.moveToFirst()) {

                int sizeIndex =
                        cursor.getColumnIndex(OpenableColumns.SIZE);

                if (sizeIndex >= 0) {
                    return cursor.getLong(sizeIndex);
                }
            }

        } catch (Exception e) {

            e.printStackTrace();

        } finally {

            if (cursor != null) {
                cursor.close();
            }
        }

        return 0;
    }

    private String getFileName(Uri uri) {

        String fileName = null;

        Cursor cursor = null;

        try {

            cursor = getContentResolver().query(
                    uri,
                    null,
                    null,
                    null,
                    null
            );

            if (cursor != null && cursor.moveToFirst()) {

                int nameIndex =
                        cursor.getColumnIndex(OpenableColumns.DISPLAY_NAME);

                if (nameIndex >= 0) {
                    fileName = cursor.getString(nameIndex);
                }
            }

        } catch (Exception e) {

            e.printStackTrace();

        } finally {

            if (cursor != null) {
                cursor.close();
            }
        }

        if (fileName == null) {
            fileName = "document";
        }

        return fileName;
    }

    private void uploadSelectedDocument(
            Uri fileUri,
            String documentType) {

        if (fileUri == null) {

            Toast.makeText(
                    this,
                    "Please select a file first.",
                    Toast.LENGTH_SHORT
            ).show();

            return;
        }

        int studentId = sessionManager.getStudentId();

        if (studentId <= 0) {

            Toast.makeText(
                    this,
                    "Student login information not found.",
                    Toast.LENGTH_LONG
            ).show();

            return;
        }

        Toast.makeText(
                this,
                "Uploading " + documentType + "...",
                Toast.LENGTH_SHORT
        ).show();

        executorService.execute(() ->
                performUpload(
                        fileUri,
                        documentType,
                        studentId
                )
        );
    }

    private void performUpload(
            Uri fileUri,
            String documentType,
            int studentId) {

        HttpURLConnection connection = null;

        String boundary =
                "----AndroidFormBoundary" +
                        System.currentTimeMillis();

        try {

            URL url = new URL(
                    ApiClient.BASE_URL + "documents.php"
            );

            connection =
                    (HttpURLConnection) url.openConnection();

            connection.setRequestMethod("POST");

            connection.setDoOutput(true);

            connection.setDoInput(true);

            connection.setUseCaches(false);

            connection.setConnectTimeout(15000);

            connection.setReadTimeout(30000);

            connection.setRequestProperty(
                    "Content-Type",
                    "multipart/form-data; boundary=" + boundary
            );

            connection.setRequestProperty(
                    "Accept",
                    "application/json"
            );

            OutputStream outputStream =
                    new BufferedOutputStream(
                            connection.getOutputStream()
                    );

            writeFormField(
                    outputStream,
                    boundary,
                    "student_id",
                    String.valueOf(studentId)
            );

            writeFormField(
                    outputStream,
                    boundary,
                    "document_type",
                    documentType
            );

            String fileName =
                    getFileName(fileUri);

            String mimeType =
                    getContentResolver().getType(fileUri);

            if (mimeType == null) {
                mimeType = getMimeTypeFromFileName(fileName);
            }

            writeFileField(
                    outputStream,
                    boundary,
                    "document_file",
                    fileName,
                    mimeType,
                    fileUri
            );

            outputStream.write(
                    ("--" + boundary + "--\r\n")
                            .getBytes("UTF-8")
            );

            outputStream.flush();
            outputStream.close();

            int responseCode =
                    connection.getResponseCode();

            InputStream responseStream;

            if (responseCode >= 200 && responseCode < 400) {

                responseStream =
                        connection.getInputStream();

            } else {

                responseStream =
                        connection.getErrorStream();
            }

            String response =
                    readResponse(responseStream);

            handleUploadResponse(
                    responseCode,
                    response,
                    documentType
            );

        } catch (Exception e) {

            e.printStackTrace();

            runOnUiThread(() ->
                    Toast.makeText(
                            RequiredDocumentsActivity.this,
                            "Upload failed: " + e.getMessage(),
                            Toast.LENGTH_LONG
                    ).show()
            );

        } finally {

            if (connection != null) {
                connection.disconnect();
            }
        }
    }

    private void writeFormField(
            OutputStream outputStream,
            String boundary,
            String fieldName,
            String value) throws Exception {

        StringBuilder builder =
                new StringBuilder();

        builder.append("--")
                .append(boundary)
                .append("\r\n");

        builder.append(
                "Content-Disposition: form-data; name=\""
        )
                .append(fieldName)
                .append("\"\r\n");

        builder.append("\r\n");

        builder.append(value);

        builder.append("\r\n");

        outputStream.write(
                builder.toString().getBytes("UTF-8")
        );
    }

    private void writeFileField(
            OutputStream outputStream,
            String boundary,
            String fieldName,
            String fileName,
            String mimeType,
            Uri fileUri) throws Exception {

        StringBuilder builder =
                new StringBuilder();

        builder.append("--")
                .append(boundary)
                .append("\r\n");

        builder.append(
                "Content-Disposition: form-data; name=\""
        )
                .append(fieldName)
                .append("\"; filename=\"")
                .append(fileName)
                .append("\"\r\n");

        builder.append("Content-Type: ")
                .append(mimeType)
                .append("\r\n");

        builder.append("\r\n");

        outputStream.write(
                builder.toString().getBytes("UTF-8")
        );

        InputStream inputStream =
                new BufferedInputStream(
                        getContentResolver()
                                .openInputStream(fileUri)
                );

        byte[] buffer = new byte[4096];

        int bytesRead;

        while ((bytesRead =
                inputStream.read(buffer)) != -1) {

            outputStream.write(
                    buffer,
                    0,
                    bytesRead
            );
        }

        inputStream.close();

        outputStream.write(
                "\r\n".getBytes("UTF-8")
        );
    }

    private String readResponse(
            InputStream inputStream) throws Exception {

        if (inputStream == null) {
            return "";
        }

        BufferedReader reader =
                new BufferedReader(
                        new InputStreamReader(
                                inputStream,
                                "UTF-8"
                        )
                );

        StringBuilder response =
                new StringBuilder();

        String line;

        while ((line = reader.readLine()) != null) {
            response.append(line);
        }

        reader.close();

        return response.toString();
    }

    private void handleUploadResponse(
            int responseCode,
            String response,
            String documentType) {

        runOnUiThread(() -> {

            try {

                if (response == null ||
                        response.trim().isEmpty()) {

                    Toast.makeText(
                            RequiredDocumentsActivity.this,
                            "Server returned no response.",
                            Toast.LENGTH_LONG
                    ).show();

                    return;
                }

                JSONObject json =
                        new JSONObject(response);

                boolean success =
                        json.optBoolean(
                                "success",
                                false
                        );

                String message =
                        json.optString(
                                "message",
                                "Upload completed."
                        );

                Toast.makeText(
                        RequiredDocumentsActivity.this,
                        message,
                        Toast.LENGTH_LONG
                ).show();

                if (success) {
                    clearUploadedDocument(documentType);
                }

            } catch (Exception e) {

                Toast.makeText(
                        RequiredDocumentsActivity.this,
                        "Server response: " + response,
                        Toast.LENGTH_LONG
                ).show();
            }
        });
    }

    private void clearUploadedDocument(
            String documentType) {

        if (documentType.equals("Aadhaar Card")) {

            aadhaarUri = null;
            tvAadhaarFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadAadhaar.setEnabled(false);

        } else if (documentType.equals("Income Certificate")) {

            incomeUri = null;
            tvIncomeFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadIncome.setEnabled(false);

        } else if (documentType.equals("Bonafide Certificate")) {

            bonafideUri = null;
            tvBonafideFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadBonafide.setEnabled(false);

        } else if (documentType.equals("Caste Certificate")) {

            casteUri = null;
            tvCasteFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadCaste.setEnabled(false);

        } else if (documentType.equals("Domicile Certificate")) {

            domicileUri = null;
            tvDomicileFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadDomicile.setEnabled(false);

        } else if (documentType.equals("Bank Passbook Copy")) {

            bankUri = null;
            tvBankFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadBank.setEnabled(false);

        } else if (documentType.equals("Previous Year Marksheet")) {

            marksheetUri = null;
            tvMarksheetFileName.setText(
                    getString(R.string.msg_uploaded_success)
            );
            btnUploadMarksheet.setEnabled(false);
        }
    }

    private String getMimeTypeFromFileName(
            String fileName) {

        String lowerName =
                fileName.toLowerCase();

        if (lowerName.endsWith(".pdf")) {
            return "application/pdf";
        }

        if (lowerName.endsWith(".png")) {
            return "image/png";
        }

        if (lowerName.endsWith(".jpg") ||
                lowerName.endsWith(".jpeg")) {
            return "image/jpeg";
        }

        return "application/octet-stream";
    }

    @Override
    protected void onDestroy() {

        super.onDestroy();

        executorService.shutdown();
    }
}