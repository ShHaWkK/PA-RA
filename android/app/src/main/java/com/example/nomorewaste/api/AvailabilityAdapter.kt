package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class AvailabilityAdapter(private val availabilities: List<Availability>) :
    RecyclerView.Adapter<AvailabilityAdapter.ViewHolder>() {

    class ViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val dayOfWeekTextView: TextView = view.findViewById(R.id.day_of_week)
        val startTimeTextView: TextView = view.findViewById(R.id.start_time)
        val endTimeTextView: TextView = view.findViewById(R.id.end_time)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_availability, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        val availability = availabilities[position]
        holder.dayOfWeekTextView.text = availability.day_of_week
        holder.startTimeTextView.text = availability.start_time
        holder.endTimeTextView.text = availability.end_time
    }

    override fun getItemCount() = availabilities.size
}