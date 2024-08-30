// Path: src/main/java/com/example/nomorewaste/api/CalendarAdapter.kt
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
    private val today: Date,
    private val onDateSelected: (Date) -> Unit
) : RecyclerView.Adapter<CalendarAdapter.CalendarViewHolder>() {

    private val dates: MutableList<Date> = mutableListOf()
    private var selectedDate: Date = today

    init {
        generateInitialDates()
    }

    private fun generateInitialDates() {
        val calendar = Calendar.getInstance()
        calendar.time = today
        for (i in -15..15) {
            calendar.add(Calendar.DAY_OF_YEAR, i)
            dates.add(calendar.time)
            calendar.time = today
        }
    }

    class CalendarViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val dayOfWeek: TextView = view.findViewById(R.id.tv_day_of_week)
        val date: TextView = view.findViewById(R.id.tv_date)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CalendarViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_calendar_day, parent, false)
        return CalendarViewHolder(view)
    }

    override fun onBindViewHolder(holder: CalendarViewHolder, position: Int) {
        val date = dates[position]
        val calendar = Calendar.getInstance()
        calendar.time = date
        val dayFormat = SimpleDateFormat("EEE", Locale.getDefault())
        val dateFormat = SimpleDateFormat("dd MMM", Locale.getDefault())

        holder.dayOfWeek.text = dayFormat.format(date)
        holder.date.text = dateFormat.format(date)

        if (selectedDate == date) {
            holder.itemView.setBackgroundResource(R.drawable.selected_date_background)
        } else {
            holder.itemView.setBackgroundResource(0)
        }

        holder.itemView.setOnClickListener {
            selectedDate = date
            notifyDataSetChanged()
            onDateSelected(date)
        }
    }

    override fun getItemCount(): Int = dates.size

    fun addMoreDates(older: Boolean) {
        val calendar = Calendar.getInstance()
        if (older) {
            val firstDate = dates.first()
            calendar.time = firstDate
            for (i in 1..15) {
                calendar.add(Calendar.DAY_OF_YEAR, -1)
                dates.add(0, calendar.time)
            }
        } else {
            val lastDate = dates.last()
            calendar.time = lastDate
            for (i in 1..15) {
                calendar.add(Calendar.DAY_OF_YEAR, 1)
                dates.add(calendar.time)
            }
        }
        notifyDataSetChanged()
    }
}
