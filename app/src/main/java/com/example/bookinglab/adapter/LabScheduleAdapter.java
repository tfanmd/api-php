package com.example.bookinglab.adapter;

import android.content.Context;
import android.graphics.Color;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.recyclerview.widget.RecyclerView;

import com.example.bookinglab.R;
import com.example.bookinglab.model.LabSchedule;
import com.example.bookinglab.model.TimeSlot;
import com.google.android.flexbox.FlexboxLayout;
import java.util.List;

public class LabScheduleAdapter extends RecyclerView.Adapter<LabScheduleAdapter.LabViewHolder> {

    private Context context;
    private List<LabSchedule> labScheduleList;
    private OnTimeSlotClickListener listener;

    public interface OnTimeSlotClickListener {
        void onTimeSlotClick(LabSchedule lab, TimeSlot timeSlot);
    }

    // BARU: Metode untuk set listener dari Activity
    public void setOnTimeSlotClickListener(OnTimeSlotClickListener listener) {
        this.listener = listener;
    }

    public LabScheduleAdapter(Context context, List<LabSchedule> labScheduleList) {
        this.context = context;
        this.labScheduleList = labScheduleList;
    }

    @NonNull
    @Override
    public LabViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(context).inflate(R.layout.list_item_lab_schedule, parent, false);
        return new LabViewHolder(view);
    }

    // VERSI BARU (SUDAH DIPERBAIKI)
    @Override
    public void onBindViewHolder(@NonNull LabViewHolder holder, int position) {
        LabSchedule labSchedule = labScheduleList.get(position);
        holder.textViewLabName.setText(labSchedule.getNamaLab());
        holder.textViewLabDesc.setText(labSchedule.getDeskripsi());

        holder.flexboxLayoutTimes.removeAllViews();

        // TAMBAHKAN PENGECEKAN INI
        if (labSchedule.getTimeSlots() != null) {
            for (TimeSlot timeSlot : labSchedule.getTimeSlots()) {
                //   ... (Seluruh kode untuk membuat Button ada di dalam blok if ini) ...

                Button timeButton = new Button(context);
                timeButton.setText(timeSlot.getSlot());

                if (timeSlot.isAvailable()) {
                    timeButton.setEnabled(true);
                    timeButton.setBackgroundColor(Color.parseColor("#4CAF50")); // Hijau
                } else {
                    timeButton.setEnabled(false);
                    timeButton.setBackgroundColor(Color.parseColor("#BDBDBD")); // Abu-abu
                }

                timeButton.setTextColor(Color.WHITE);

                if (timeSlot.isAvailable()) {
                    timeButton.setOnClickListener(v -> {
                        // Cek apakah listener sudah dipasang oleh Activity
                        if (listener != null) {
                            // Panggil metode di Activity, kirim data lab dan slot waktu yang di-klik
                            listener.onTimeSlotClick(labSchedule, timeSlot);
                        }
                    });
                }

                FlexboxLayout.LayoutParams params = new FlexboxLayout.LayoutParams(
                        FlexboxLayout.LayoutParams.WRAP_CONTENT,
                        FlexboxLayout.LayoutParams.WRAP_CONTENT
                );
                params.setMargins(0, 0, 16, 16);
                timeButton.setLayoutParams(params);

                holder.flexboxLayoutTimes.addView(timeButton);
            }
        }
    }

    @Override
    public int getItemCount() {
        return labScheduleList.size();
    }

    static class LabViewHolder extends RecyclerView.ViewHolder {
        TextView textViewLabName, textViewLabDesc;
        FlexboxLayout flexboxLayoutTimes;

        public LabViewHolder(@NonNull View itemView) {
            super(itemView);
            textViewLabName = itemView.findViewById(R.id.textViewLabName);
            textViewLabDesc = itemView.findViewById(R.id.textViewLabDesc);
            flexboxLayoutTimes = itemView.findViewById(R.id.flexboxLayoutTimes);
        }
    }
}

