package com.example.nomorewaste.api

import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R
import com.example.nomorewaste.api.Availability

class AvailabilityAdapter(private val availabilities: List<Availability>) :
    RecyclerView.Adapter<AvailabilityAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val timeSlotTextView: TextView = view.findViewById(R.id.time_slot)
        val activityDescriptionTextView: TextView = view.findViewById(R.id.activity_description)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_availability, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val availability = availabilities[position]
        holder.timeSlotTextView.text = "${availability.start_time} - ${availability.end_time}"
        holder.activityDescriptionTextView.text = availability.day_of_week
    }

    override fun getItemCount() = availabilities.size
}
