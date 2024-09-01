package com.example.nomorewaste.api

import android.util.Log
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R
import com.example.nomorewaste.model.PlanningItem

class PlanningAdapter(private var planningItems: List<PlanningItem>) : RecyclerView.Adapter<PlanningAdapter.PlanningViewHolder>() {

    fun updateData(newItems: List<PlanningItem>) {
        this.planningItems = newItems
        notifyDataSetChanged()

        // Ajoutez un log pour vérifier la mise à jour des données
        Log.d("PlanningAdapter", "Updating data with ${newItems.size} items")
    }


    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): PlanningViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_planning, parent, false)
        return PlanningViewHolder(view)
    }

    override fun onBindViewHolder(holder: PlanningViewHolder, position: Int) {
        val item = planningItems[position]
        holder.bind(item)
    }

    override fun getItemCount(): Int = planningItems.size

    class PlanningViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val titleTextView: TextView = itemView.findViewById(R.id.planning_title)
        private val dateTextView: TextView = itemView.findViewById(R.id.planning_date)

        fun bind(item: PlanningItem) {
            titleTextView.text = item.title
            dateTextView.text = item.dateRange
        }
    }
}
