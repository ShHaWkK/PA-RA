package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class PlanningAdapter(
    private val plannings: List<ServiceSchedule>
) : RecyclerView.Adapter<PlanningAdapter.PlanningViewHolder>() {

    class PlanningViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val serviceName: TextView = view.findViewById(R.id.planning_service_name)
        val startTime: TextView = view.findViewById(R.id.planning_service_start_time)
        val endTime: TextView = view.findViewById(R.id.planning_service_end_time)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PlanningViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_planning, parent, false)
        return PlanningViewHolder(view)
    }

    override fun onBindViewHolder(holder: PlanningViewHolder, position: Int) {
        val planning = plannings[position]
        holder.serviceName.text = planning.service.name
        holder.startTime.text = "Start Time: ${planning.startTime}"
        holder.endTime.text = "End Time: ${planning.endTime}"
    }

    override fun getItemCount(): Int = plannings.size
}
