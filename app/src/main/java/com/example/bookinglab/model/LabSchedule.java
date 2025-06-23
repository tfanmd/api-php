package com.example.bookinglab.model;

import com.google.gson.annotations.SerializedName; // <-- IMPORT INI
import java.util.List;

public class LabSchedule {
    @SerializedName("id") // Meskipun namanya sama, lebih baik tetap eksplisit
    private int id;

    @SerializedName("nama_lab") // <-- TAMBAHKAN INI
    private String namaLab;

    @SerializedName("deskripsi") // Ini juga sama, tapi kita buat eksplisit
    private String deskripsi;

    @SerializedName("time_slots") // <-- TAMBAHKAN INI
    private List<TimeSlot> timeSlots;

    // ... (kode getter tidak perlu diubah) ...
    public int getId() { return id; }
    public String getNamaLab() { return namaLab; }
    public String getDeskripsi() { return deskripsi; }
    public List<TimeSlot> getTimeSlots() { return timeSlots; }
}