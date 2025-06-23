package com.example.bookinglab;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.example.bookinglab.R;
import com.example.bookinglab.util.SharedPrefManager;

import org.json.JSONException;
import org.json.JSONObject;

public class BookingFormActivity extends AppCompatActivity {

    // Deklarasi semua komponen UI
    TextView textViewNamaDosen, textViewJamTanggal, textViewTempat;
    EditText editTextNamaMatkul, editTextSks, editTextDeskripsi, editTextKelompokMatkul;
    Button buttonKirim;
    RequestQueue requestQueue;

    // Variabel untuk menyimpan data yang diterima dan data user
    private int labId;
    private int userId;
    private String selectedDate;
    private String selectedSlot;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_booking_form);

        // Inisialisasi komponen UI
        textViewNamaDosen = findViewById(R.id.textViewNamaDosen);
        textViewJamTanggal = findViewById(R.id.textViewJamTanggal);
        textViewTempat = findViewById(R.id.textViewTempat);
        editTextNamaMatkul = findViewById(R.id.editTextNamaMatkul);
        editTextSks = findViewById(R.id.editTextSks);
        editTextDeskripsi = findViewById(R.id.editTextDeskripsi);
        editTextKelompokMatkul = findViewById(R.id.editTextNamaKelompok);
        buttonKirim = findViewById(R.id.buttonKirim);
        requestQueue = Volley.newRequestQueue(this);

        // --- Menerima dan Menampilkan Data ---
        // Ambil data dari Intent
        labId = getIntent().getIntExtra("LAB_ID", -1);
        String labName = getIntent().getStringExtra("LAB_NAME");
        selectedDate = getIntent().getStringExtra("SELECTED_DATE");
        selectedSlot = getIntent().getStringExtra("SELECTED_SLOT");

        // Ambil data user dari SharedPreferences
        userId = SharedPrefManager.getInstance(this).getUser().getId();
        String namaDosen = SharedPrefManager.getInstance(this).getUser().getNamaLengkap();

        // Set data ke TextViews
        textViewNamaDosen.setText(namaDosen);
        textViewTempat.setText(labName);
        textViewJamTanggal.setText(selectedSlot + ", " + selectedDate);
        // ------------------------------------


        // --- Logika Tombol Kirim ---
        buttonKirim.setOnClickListener(v -> {
            submitBooking();
        });
    }

    private void submitBooking() {
        // Ambil data dari EditText
        String namaMatkul = editTextNamaMatkul.getText().toString().trim();
        String KelompokMatkul = editTextKelompokMatkul.getText().toString().trim();
        String sks = editTextSks.getText().toString().trim();
        String deskripsi = editTextDeskripsi.getText().toString().trim();

        // Validasi input tidak boleh kosong
        if (namaMatkul.isEmpty() || sks.isEmpty() || KelompokMatkul.isEmpty()) {
            Toast.makeText(this, "Semua field wajib diisi kecuali deskripsi", Toast.LENGTH_SHORT).show();
            return;
        }

        // Format waktu_mulai dan waktu_selesai
        String[] times = selectedSlot.split("-");
        String waktuMulai = selectedDate + " " + times[0] + ":00";
        String waktuSelesai = selectedDate + " " + times[1] + ":00";

        // Ganti dengan IP Anda
        String url = "http://192.168.85.165/api-moprog/api/dosen/submit_booking.php";

        // Buat JSON Object untuk dikirim
        JSONObject requestBody = new JSONObject();
        try {
            requestBody.put("user_id", userId);
            requestBody.put("lab_id", labId);
            requestBody.put("waktu_mulai", waktuMulai);
            requestBody.put("waktu_selesai", waktuSelesai);
            requestBody.put("nama_matkul", namaMatkul);
            requestBody.put("kelompok_matkul", KelompokMatkul);
            requestBody.put("sks", Integer.parseInt(sks));
            requestBody.put("deskripsi", deskripsi);
        } catch (JSONException e) {
            e.printStackTrace();
        }

        // Buat Volley Request
        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.POST, url, requestBody,
                response -> {
                    try {
                        boolean status = response.getBoolean("status");
                        String message = response.getString("message");
                        Toast.makeText(BookingFormActivity.this, message, Toast.LENGTH_LONG).show();

                        if (status) {
                            // Jika berhasil, kembali ke MainActivity dan tutup halaman form
                            Intent intent = new Intent(BookingFormActivity.this, MainActivity.class);
                            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
                            startActivity(intent);
                            finish();
                        }
                    } catch (JSONException e) {
                        e.printStackTrace();
                    }
                },
                error -> {
                    Toast.makeText(BookingFormActivity.this, "Terjadi kesalahan. Coba lagi.", Toast.LENGTH_SHORT).show();
                });

        requestQueue.add(jsonObjectRequest);
    }
}