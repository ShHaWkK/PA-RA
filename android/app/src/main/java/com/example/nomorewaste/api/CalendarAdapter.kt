package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R
import java.text.SimpleDateFormat
import java.util.*

class CalendarAdapter(
    private val dates: List<Date>,
    private val onDateSelected: (Date) -> Unit
) : RecyclerView.Adapter<CalendarAdapter.CalendarViewHolder>() {

    class CalendarViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        private val dayOfWeek: TextView = view.findViewById(R.id.tv_day_of_week)
        private val date: TextView = view.findViewById(R.id.tv_date)

        // Ajoutez une méthode bind ici
        fun bind(date: Date) {
            val dateFormat = SimpleDateFormat("EEE", Locale.getDefault())
            val dayFormat = SimpleDateFormat("dd", Locale.getDefault())
            dayOfWeek.text = dateFormat.format(date)
            this.date.text = dayFormat.format(date)
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CalendarViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_calendar_day, parent, false)
        return CalendarViewHolder(view)
    }

    override fun onBindViewHolder(holder: CalendarViewHolder, position: Int) {
        val date = dates[position]
        holder.bind(date) // Appelez la méthode bind
        holder.itemView.setOnClickListener {
            onDateSelected(date)
        }
    }

    override fun getItemCount(): Int = dates.size
}
