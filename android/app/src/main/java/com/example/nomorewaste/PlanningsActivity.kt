package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.CalendarAdapter
import com.example.nomorewaste.api.PlanningAdapter
import com.example.nomorewaste.viewmodel.PlanningViewModel
import java.util.*

class PlanningsActivity : AppCompatActivity() {

    private val planningViewModel: PlanningViewModel by viewModels()
    private lateinit var recyclerViewCalendar: RecyclerView
    private lateinit var recyclerViewPlannings: RecyclerView
    private lateinit var calendarAdapter: CalendarAdapter
    private lateinit var planningAdapter: PlanningAdapter
    private val calendar = Calendar.getInstance()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        recyclerViewCalendar = findViewById(R.id.recycler_view_calendar)
        recyclerViewCalendar.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)

        recyclerViewPlannings = findViewById(R.id.recycler_view_plannings)
        recyclerViewPlannings.layoutManager = LinearLayoutManager(this)

        planningAdapter = PlanningAdapter(emptyList())
        recyclerViewPlannings.adapter = planningAdapter

        val dates = generateWeekDates()
        // Adapter pour les dates
        calendarAdapter = CalendarAdapter(dates) { date ->
            val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
            val userId = sharedPreferences.getInt("USER_ID", -1)

            if (userId != -1) {
                planningViewModel.loadUserScheduleForDate(userId, date) // Charge les plannings pour la date sélectionnée
            } else {
                Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
            }
        }

        recyclerViewCalendar.adapter = calendarAdapter

        planningViewModel.schedules.observe(this, Observer { plannings ->
            if (plannings != null && plannings.isNotEmpty()) {
                planningAdapter.updateData(plannings) // Met à jour l'adaptateur avec les nouveaux plannings
            } else {
                Toast.makeText(this, "Aucun planning trouvé", Toast.LENGTH_SHORT).show()
            }
        })


        planningViewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, "Error: $errorMessage", Toast.LENGTH_SHORT).show()
            }
        })

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        if (userId != -1) {
            planningViewModel.loadUserSchedule(userId)
        } else {
            Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
        }
    }

    private fun generateWeekDates(): List<Date> {
        val dates = mutableListOf<Date>()
        val calendar = Calendar.getInstance()
        calendar.set(Calendar.DAY_OF_WEEK, calendar.firstDayOfWeek)
        for (i in 0..6) {
            dates.add(calendar.time)
            calendar.add(Calendar.DATE, 1)
        }
        return dates
    }
}
