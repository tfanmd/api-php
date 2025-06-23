package com.example.bookinglab.util;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;

import com.example.bookinglab.LoginActivity;
import com.example.bookinglab.model.User;
public class SharedPrefManager {

    private static SharedPrefManager instance;
    private static Context ctx;

    private static final String SHARED_PREF_NAME = "bookinglabpref";
    private static final String KEY_USER_ID = "keyuserid";
    private static final String KEY_USERNAME = "keyusername";
    private static final String KEY_NAMA_LENGKAP = "keynamalengkap";
    private static final String KEY_USER_ROLE = "keyuserrole";

    private SharedPrefManager(Context context) {
        ctx = context;
    }

    public static synchronized SharedPrefManager getInstance(Context context) {
        if (instance == null) {
            instance = new SharedPrefManager(context);
        }
        return instance;
    }

    // Metode untuk menyimpan data user saat login
    public void userLogin(User user) {
        SharedPreferences sharedPreferences = ctx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt(KEY_USER_ID, user.getId());
        editor.putString(KEY_USERNAME, user.getUsername());
        editor.putString(KEY_NAMA_LENGKAP, user.getNamaLengkap());
        editor.putString(KEY_USER_ROLE, user.getRole());
        editor.apply();
    }

    // Metode untuk mengecek apakah user sudah login
    public boolean isLoggedIn() {
        SharedPreferences sharedPreferences = ctx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        // Jika username tidak null, artinya sudah login
        return sharedPreferences.getString(KEY_USERNAME, null) != null;
    }

    // Metode untuk mendapatkan data user yang sedang login
    public User getUser() {
        SharedPreferences sharedPreferences = ctx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        return new User(
                sharedPreferences.getInt(KEY_USER_ID, -1),
                sharedPreferences.getString(KEY_USERNAME, null),
                sharedPreferences.getString(KEY_NAMA_LENGKAP, null),
                sharedPreferences.getString(KEY_USER_ROLE, null)
        );
    }

    // Metode untuk logout
    public void logout() {
        SharedPreferences sharedPreferences = ctx.getSharedPreferences(SHARED_PREF_NAME, Context.MODE_PRIVATE);
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.clear(); // Menghapus semua data sesi
        editor.apply();

        // BARIS-BARIS INI YANG BERTUGAS PINDAH KE HALAMAN LOGIN
        Intent intent = new Intent(ctx, LoginActivity.class);
        // Flag ini penting untuk memulai activity baru dan membersihkan activity sebelumnya
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        ctx.startActivity(intent);
    }
}
