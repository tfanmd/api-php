package com.example.bookinglab;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.example.bookinglab.model.User;
import com.example.bookinglab.util.SharedPrefManager;

import org.json.JSONException;
import org.json.JSONObject;


import android.os.Bundle;

public class LoginActivity extends AppCompatActivity {

    private EditText editTextUsername, editTextPassword;
    private Button buttonLogin;
    private ProgressBar progressBar;
    private RequestQueue requestQueue;

    private final String LOGIN_URL = "http://192.168.85.165/api-moprog/api/login.php";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_login);

        editTextUsername = findViewById(R.id.editTextUsername);
        editTextPassword = findViewById(R.id.editTextPassword);
        buttonLogin = findViewById(R.id.buttonLogin);
        progressBar = findViewById(R.id.progressBar);

        requestQueue = Volley.newRequestQueue(this);

        buttonLogin.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                loginUser();
            }
        });
    }

    @Override
    protected void onStart() {
        super.onStart();
        // Cek jika user sudah login, langsung arahkan ke MainActivity
        if (SharedPrefManager.getInstance(this).isLoggedIn()) {
            Intent intent = new Intent(this, MainActivity.class);
            intent.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
            startActivity(intent);
        }
    }

    private void loginUser() {
        String username = editTextUsername.getText().toString().trim();
        String password = editTextPassword.getText().toString().trim();

        if (username.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Username dan Password tidak boleh kosong", Toast.LENGTH_SHORT).show();
            return;
        }

        progressBar.setVisibility(View.VISIBLE);
        buttonLogin.setEnabled(false);

        // Membuat JSON Object untuk dikirim sebagai body request
        JSONObject requestBody = new JSONObject();
        try {
            requestBody.put("username", username);
            requestBody.put("password", password);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.POST, LOGIN_URL, requestBody,
                new Response.Listener<JSONObject>() {
                    @Override
                    public void onResponse(JSONObject response) {
                        progressBar.setVisibility(View.GONE);
                        buttonLogin.setEnabled(true);
                        try {
                            boolean status = response.getBoolean("status");
                            String message = response.getString("message");
                            Toast.makeText(LoginActivity.this, message, Toast.LENGTH_LONG).show();

                            if (status) {
                                // Ambil semua data user dari JSON
                                int id = response.getInt("id");
                                String usernameFromJson = response.getString("username");
                                String namaLengkap = response.getString("nama_lengkap");
                                String role = response.getString("role");

                                // Buat objek User
                                User user = new User(id, usernameFromJson, namaLengkap, role);

                                // Simpan sesi user menggunakan SharedPrefManager
                                SharedPrefManager.getInstance(getApplicationContext()).userLogin(user);

                                // Arahkan ke activity yang sesuai
                                Intent intent;
                                if (role.equals("admin")) {
                                    // TODO: Buat AdminDashboardActivity.java
                                    // intent = new Intent(LoginActivity.this, AdminDashboardActivity.class);
                                    Toast.makeText(LoginActivity.this, "Login Admin Belum Dibuat", Toast.LENGTH_SHORT).show();
                                } else {
                                    intent = new Intent(LoginActivity.this, MainActivity.class);
                                    startActivity(intent);
                                    finish(); // Tutup LoginActivity agar tidak bisa kembali
                                }
                            }

                        } catch (JSONException e) {
                            e.printStackTrace();
                            Toast.makeText(LoginActivity.this, "Error parsing data!", Toast.LENGTH_SHORT).show();
                        }
                    }
                },
                new Response.ErrorListener() {
                    @Override
                    public void onErrorResponse(VolleyError error) {
                        progressBar.setVisibility(View.GONE);
                        buttonLogin.setEnabled(true);
                        // Cek jika ada response network dan coba baca pesannya
                        if (error.networkResponse != null && error.networkResponse.data != null) {
                            try {
                                String responseBody = new String(error.networkResponse.data, "utf-8");
                                JSONObject data = new JSONObject(responseBody);
                                String message = data.getString("message");
                                Toast.makeText(LoginActivity.this, message, Toast.LENGTH_LONG).show();
                            } catch (Exception e) {
                                e.printStackTrace();
                            }
                        } else {
                            Toast.makeText(LoginActivity.this, "Login Gagal! Cek koneksi internet Anda.", Toast.LENGTH_SHORT).show();
                        }
                    }
                });

        // Menambahkan request ke RequestQueue
        requestQueue.add(jsonObjectRequest);
    }
}