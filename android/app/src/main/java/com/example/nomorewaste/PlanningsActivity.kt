package com.example.nomorewaste

import android.os.Bundle
import android.widget.CalendarView
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.PlanningAdapter
import com.example.nomorewaste.viewmodel.PlanningViewModel
import java.text.SimpleDateFormat
import java.util.*

class PlanningsActivity : AppCompatActivity() {

    private val planningViewModel: PlanningViewModel by viewModels()
    private lateinit var calendarView: CalendarView
    private lateinit var recyclerViewPlannings: RecyclerView
    private lateinit var planningAdapter: PlanningAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        setupRecyclerViews()
        setupCalendarView()
        observeViewModel()
        loadUserScheduleForDate(Calendar.getInstance().time)
    }

    private fun setupRecyclerViews() {
        recyclerViewPlannings = findViewById(R.id.recycler_view_plannings)
        recyclerViewPlannings.layoutManager = LinearLayoutManager(this)
        planningAdapter = PlanningAdapter(emptyList())
        recyclerViewPlannings.adapter = planningAdapter
    }

    private fun setupCalendarView() {
        calendarView = findViewById(R.id.calendar_view)
        calendarView.setOnDateChangeListener { _, year, month, dayOfMonth ->
            val selectedDate = Calendar.getInstance()
            selectedDate.set(year, month, dayOfMonth)
            loadUserScheduleForDate(selectedDate.time)
        }
    }

    private fun observeViewModel() {
        planningViewModel.schedules.observe(this, Observer { plannings ->
            if (plannings != null && plannings.isNotEmpty()) {
                planningAdapter.updateData(plannings)
            } else {
                planningAdapter.updateData(emptyList()) // Clear the list when no data is found
                Toast.makeText(this, "Aucun planning trouvé", Toast.LENGTH_SHORT).show()
            }
        })

        planningViewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, "Erreur: $errorMessage", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun loadUserScheduleForDate(date: Date) {
        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        if (userId != -1) {
            planningViewModel.loadUserScheduleForDate(userId, date)
        } else {
            Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
        }
    }
}
