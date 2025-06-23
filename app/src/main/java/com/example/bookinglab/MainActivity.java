package com.example.bookinglab;

import androidx.appcompat.app.AppCompatActivity;
import androidx.cardview.widget.CardView;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import com.example.bookinglab.R;
import com.example.bookinglab.model.User;
import com.example.bookinglab.util.SharedPrefManager;

public class MainActivity extends AppCompatActivity {

    TextView textViewWelcome;
    Button buttonLogout;
    CardView cardJadwalBooking, cardRiwayatBooking;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        // Panggil SharedPrefManager untuk mengecek status login
        // Jika tidak login, arahkan kembali ke LoginActivity
        if (!SharedPrefManager.getInstance(this).isLoggedIn()) {
            finish();
            startActivity(new Intent(this, LoginActivity.class));
            return;
        }

        textViewWelcome = findViewById(R.id.textViewWelcome);
        buttonLogout = findViewById(R.id.buttonLogout);
        // BARU: Inisialisasi CardView menggunakan ID dari XML
        cardJadwalBooking = findViewById(R.id.cardBooking);
        cardRiwayatBooking = findViewById(R.id.cardRiwayatBooking);

        // Ambil data user dari SharedPrefManager
        User user = SharedPrefManager.getInstance(this).getUser();

        // Tampilkan pesan selamat datang dengan nama pengguna
        textViewWelcome.setText("Selamat Datang, " + user.getNamaLengkap());

        // BARU: Menambahkan OnClickListener untuk setiap kartu
        cardJadwalBooking.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Pindah ke halaman BookingScheduleActivity
                Intent intent = new Intent(MainActivity.this, BookingActivity.class);
                startActivity(intent);
            }
        });

        cardRiwayatBooking.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Pindah ke halaman BookingHistoryActivity
                Intent intent = new Intent(MainActivity.this, RiwayatBookingActivity.class);
                startActivity(intent);
            }
        });

        // Atur fungsi untuk tombol logout
        buttonLogout.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Tampilkan pesan Toast
                Toast.makeText(MainActivity.this, "Anda telah logout", Toast.LENGTH_SHORT).show();

                // Panggil metode logout dari SharedPrefManager
                SharedPrefManager.getInstance(getApplicationContext()).logout();

                // Tutup MainActivity secara manual agar transisi lebih cepat
                finish();
            }
        });
    }
}