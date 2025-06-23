package com.example.bookinglab.model;

public class User {

    private int id;
    private String username;
    private String namaLengkap;
    private String role;

    // Constructor yang mungkin sudah Anda punya
    public User(int id, String username, String namaLengkap, String role) {
        this.id = id;
        this.username = username;
        this.namaLengkap = namaLengkap;
        this.role = role;
    }

    // Getter untuk setiap properti
    public int getId() {
        return id;
    }

    public String getUsername() {
        return username;
    }

    public String getNamaLengkap() {
        return namaLengkap;
    }

    public String getRole() {
        return role;
    }
}
