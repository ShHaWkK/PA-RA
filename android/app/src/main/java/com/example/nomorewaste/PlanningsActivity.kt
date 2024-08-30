// Path: src/main/java/com/example/nomorewaste/PlanningsActivity.kt
package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.adapter.PlanningAdapter
import com.example.nomorewaste.api.*
import com.example.nomorewaste.viewmodel.PlanningViewModel
import java.util.*

class PlanningsActivity : AppCompatActivity() {

    private val planningViewModel: PlanningViewModel by viewModels()
    private lateinit var recyclerViewCalendar: RecyclerView
    private lateinit var recyclerViewPlannings: RecyclerView
    private lateinit var calendarAdapter: CalendarAdapter
    private lateinit var planningAdapter: PlanningAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        // Setup RecyclerView for calendar dates
        recyclerViewCalendar = findViewById(R.id.recycler_view_calendar)
        recyclerViewCalendar.layoutManager = LinearLayoutManager(this, LinearLayoutManager.HORIZONTAL, false)

        // Setup RecyclerView for plannings
        recyclerViewPlannings = findViewById(R.id.recycler_view_plannings)
        recyclerViewPlannings.layoutManager = LinearLayoutManager(this)

        planningAdapter = PlanningAdapter(emptyList())
        recyclerViewPlannings.adapter = planningAdapter

        val today = Calendar.getInstance().time
        calendarAdapter = CalendarAdapter(today) { date ->
            val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
            val userId = sharedPreferences.getInt("USER_ID", -1)

            if (userId != -1) {
                planningViewModel.loadUserScheduleForDate(userId, date)
            } else {
                Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
            }
        }

        recyclerViewCalendar.adapter = calendarAdapter

        planningViewModel.schedules.observe(this, Observer { plannings ->
            if (plannings != null && plannings.isNotEmpty()) {
                planningAdapter = PlanningAdapter(plannings)
                recyclerViewPlannings.adapter = planningAdapter
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
}
