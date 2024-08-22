package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.PlanningAdapter
import com.example.nomorewaste.viewmodel.PlanningViewModel

class PlanningsActivity : AppCompatActivity() {

    private val planningViewModel: PlanningViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: PlanningAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_planning)

        recyclerView = findViewById(R.id.recycler_view_plannings)
        recyclerView.layoutManager = LinearLayoutManager(this)

        planningViewModel.schedules.observe(this, Observer { plannings ->
            if (plannings != null && plannings.isNotEmpty()) {
                adapter = PlanningAdapter(plannings)
                recyclerView.adapter = adapter
            } else {
                Toast.makeText(this, "No schedules found", Toast.LENGTH_SHORT).show()
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
