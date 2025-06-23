package com.example.bookinglab; // Ganti dengan package Anda

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.toolbox.JsonObjectRequest;
import com.android.volley.toolbox.Volley;
import com.example.bookinglab.R;
import com.example.bookinglab.adapter.LabScheduleAdapter;
import com.example.bookinglab.model.LabSchedule;
import com.example.bookinglab.model.TimeSlot;
import com.google.gson.Gson;
import com.google.gson.reflect.TypeToken;
import org.json.JSONArray;
import org.json.JSONException;
import java.lang.reflect.Type;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

public class BookingActivity extends AppCompatActivity implements LabScheduleAdapter.OnTimeSlotClickListener{

    private RecyclerView recyclerView;
    private LabScheduleAdapter adapter;
    private List<LabSchedule> labScheduleList;
    private ProgressBar progressBar;
    private TextView textViewSelectedDate;
    private String selectedDate;
    private RequestQueue requestQueue;


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_booking);

        recyclerView = findViewById(R.id.recyclerViewSchedule);
        progressBar = findViewById(R.id.progressBar);
        textViewSelectedDate = findViewById(R.id.textViewSelectedDate);
        requestQueue = Volley.newRequestQueue(this);

        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        labScheduleList = new ArrayList<>();
        adapter = new LabScheduleAdapter(this, labScheduleList);
        recyclerView.setAdapter(adapter);
        adapter.setOnTimeSlotClickListener(this);

        // Ambil jadwal untuk hari ini secara default
        String todayDate = new SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(new Date());
        fetchSchedule(todayDate);
    }

    // BARU: Implementasi metode dari interface
    @Override
    public void onTimeSlotClick(LabSchedule lab, TimeSlot timeSlot) {

        // Metode ini akan terpanggil saat tombol jam di-klik
        Intent intent = new Intent(this, BookingFormActivity.class);

        // Kirim semua data yang diperlukan ke BookingFormActivity
        intent.putExtra("LAB_ID", lab.getId());
        intent.putExtra("LAB_NAME", lab.getNamaLab());
        intent.putExtra("SELECTED_DATE", this.selectedDate);
        intent.putExtra("SELECTED_SLOT", timeSlot.getSlot());

        startActivity(intent);
    }

    private void fetchSchedule(String date) {
        this.selectedDate = date;
        textViewSelectedDate.setText("Jadwal untuk tanggal: " + date);
        progressBar.setVisibility(View.VISIBLE);
        labScheduleList.clear();

        // Ganti IP dengan IP Anda
        String url = "http://192.168.85.165/api-moprog/api/dosen/get_schedule.php?date=" + date;

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.GET, url, null,
                response -> {
                    android.util.Log.d("ScheduleAPI", "Response: " + response.toString());
                    progressBar.setVisibility(View.GONE);
                    try {
                        JSONArray records = response.getJSONArray("records");

                        // Menggunakan GSON untuk parsing JSON ke Objek Java
                        Gson gson = new Gson();
                        Type listType = new TypeToken<ArrayList<LabSchedule>>(){}.getType();
                        List<LabSchedule> fetchedList = gson.fromJson(records.toString(), listType);

                        labScheduleList.addAll(fetchedList);
                        adapter.notifyDataSetChanged();

                    } catch (JSONException e) {
                        e.printStackTrace();
                        Toast.makeText(this, "Gagal mem-parsing data", Toast.LENGTH_SHORT).show();
                    }
                },
                error -> {
                    progressBar.setVisibility(View.GONE);
                    Toast.makeText(this, "Gagal mengambil data dari server", Toast.LENGTH_SHORT).show();
                });

        requestQueue.add(jsonObjectRequest);
    }
}