// Path: src/main/java/com/example/nomorewaste/PlanningsActivity.kt
package com.example.nomorewaste

import android.content.Context
import android.os.Bundle
import android.util.Log
import android.widget.CalendarView
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.PlanningAdapter
import com.example.nomorewaste.viewmodel.PlanningViewModel
import java.util.*

class PlanningsActivity : AppCompatActivity() {

    private lateinit var calendarView: CalendarView
    private lateinit var recyclerView: RecyclerView
    private lateinit var planningAdapter: PlanningAdapter

    private val planningViewModel: PlanningViewModel by viewModels()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        calendarView = findViewById(R.id.calendar_view)
        recyclerView = findViewById(R.id.recycler_view_plannings)
        recyclerView.layoutManager = LinearLayoutManager(this)

        planningAdapter = PlanningAdapter(emptyList())
        recyclerView.adapter = planningAdapter

        val userId = getUserIdFromPreferences()
        if (userId == null) {
            Toast.makeText(this, "Erreur de récupération de l'utilisateur", Toast.LENGTH_SHORT).show()
            return
        }

        // Observer les données de planning du ViewModel
        planningViewModel.planningData.observe(this, Observer { planningItems ->
            Log.d("PlanningsActivity", "Nombre d'éléments de planning reçus : ${planningItems.size}")
            planningAdapter.updateData(planningItems)
        })

        planningViewModel.errorMessage.observe(this, Observer { errorMessage ->
            Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
        })

        // Gestionnaire de sélection de date
        calendarView.setOnDateChangeListener { _, year, month, dayOfMonth ->
            val selectedDate = Calendar.getInstance().apply {
                set(year, month, dayOfMonth)
            }.time
            Log.d("PlanningsActivity", "Date sélectionnée: $selectedDate")
            planningViewModel.fetchPlanningByUserIdAndDate(userId, selectedDate)
        }

        // Charger le planning d'aujourd'hui par défaut
        planningViewModel.fetchPlanningByUserIdAndDate(userId, Date())
    }


    private fun getUserIdFromPreferences(): Int? {
        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", Context.MODE_PRIVATE)
        return sharedPreferences.getInt("USER_ID", -1).takeIf { it != -1 }
    }
}
