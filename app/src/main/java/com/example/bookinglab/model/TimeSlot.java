package com.example.bookinglab.model;

import com.google.gson.annotations.SerializedName;

public class TimeSlot {
    @SerializedName("slot")
    private String slot;
    @SerializedName("is_available")
    private boolean isAvailable;

    public String getSlot() {
        return slot;
    }

    public boolean isAvailable() {
        return isAvailable;
    }
}
