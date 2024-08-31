package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class RegisterVolunteerAvailabilityAdapter(private val availabilities: List<AvailabilityRequest>) :
    RecyclerView.Adapter<RegisterVolunteerAvailabilityAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val timeSlotTextView: TextView = view.findViewById(R.id.time_slot)
        val dayOfWeekTextView: TextView = view.findViewById(R.id.day_of_week)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_availability, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val availability = availabilities[position]
        holder.timeSlotTextView.text = "${availability.startTime} - ${availability.endTime}"
        holder.dayOfWeekTextView.text = availability.dayOfWeek
    }

    override fun getItemCount() = availabilities.size
}
