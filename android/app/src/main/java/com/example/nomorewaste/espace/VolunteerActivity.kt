package com.example.nomorewaste.espace

import android.content.Context
import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomoreswaste.LoginActivity
import com.example.nomorewaste.AvailabilitiesActivity
import com.example.nomorewaste.PlanningsActivity
import com.example.nomorewaste.R
import com.example.nomorewaste.UserManagementActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Availability
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.User
import com.example.nomorewaste.api.AvailabilityAdapter
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class VolunteerActivity : AppCompatActivity() {

    private lateinit var nameTextView: TextView
    private lateinit var emailTextView: TextView
    private lateinit var phoneTextView: TextView
    private lateinit var recyclerView: RecyclerView
    private lateinit var apiService: ApiService

    private lateinit var buttonViewAvailabilities: Button
    private lateinit var buttonManageUser: Button
    private lateinit var buttonViewPlannings: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_volunteer)

        nameTextView = findViewById(R.id.volunteer_name)
        emailTextView = findViewById(R.id.volunteer_email)
        phoneTextView = findViewById(R.id.volunteer_phone)
        recyclerView = findViewById(R.id.recycler_view)
        buttonViewAvailabilities = findViewById(R.id.button_view_availabilities)
        buttonManageUser = findViewById(R.id.button_manage_user)
        buttonViewPlannings = findViewById(R.id.button_view_plannings)

        recyclerView.layoutManager = LinearLayoutManager(this)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", Context.MODE_PRIVATE)
        val volunteerId = sharedPreferences.getInt("USER_ID", -1)
        Log.d("VolunteerActivity", "Stored Volunteer ID: $volunteerId")

        if (volunteerId != -1) {
            getVolunteerDetails(volunteerId)
        } else {
            Toast.makeText(this, "Erreur de récupération de l'ID du bénévole", Toast.LENGTH_SHORT).show()
            val intent = Intent(this, LoginActivity::class.java)
            startActivity(intent)
            finish()
        }

        buttonViewAvailabilities.setOnClickListener {
            Log.d("VolunteerDashboard", "buttonViewAvailabilities clicked")
            if (volunteerId != -1) {
                val intent = Intent(this, AvailabilitiesActivity::class.java)
                startActivity(intent)
            }
        }

        buttonManageUser.setOnClickListener {
            Log.d("VolunteerDashboard", "buttonManageUser clicked")
            val intent = Intent(this, UserManagementActivity::class.java)
            startActivity(intent)
        }

        buttonViewPlannings.setOnClickListener {
            Log.d("VolunteerDashboard", "buttonViewPlannings clicked")
            val intent = Intent(this, PlanningsActivity::class.java)
            startActivity(intent)
        }
    }

    private fun getVolunteerDetails(volunteerId: Int) {
        apiService.getUser(volunteerId).enqueue(object : Callback<User> {
            override fun onResponse(call: Call<User>, response: Response<User>) {
                if (response.isSuccessful) {
                    val volunteer = response.body()
                    volunteer?.let {
                        nameTextView.text = getString(R.string.volunteer_name, it.firstName, it.lastName)
                        emailTextView.text = getString(R.string.volunteer_email, it.email)
                        phoneTextView.text = getString(R.string.volunteer_phone, it.phoneNumber)
                    }
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e("VolunteerDashboard", "Error: $errorBody, Code: ${response.code()}")
                    Toast.makeText(this@VolunteerActivity, "Erreur de récupération des détails du bénévole: $errorBody", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<User>, t: Throwable) {
                Log.e("VolunteerDashboard", "Failure: ${t.message}", t)
                Toast.makeText(this@VolunteerActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun getVolunteerAvailabilities(volunteerId: Int) {
        apiService.getAvailabilities(volunteerId).enqueue(object : Callback<List<Availability>> {
            override fun onResponse(call: Call<List<Availability>>, response: Response<List<Availability>>) {
                if (response.isSuccessful) {
                    val availabilities = response.body() ?: emptyList()
                    recyclerView.adapter = AvailabilityAdapter(availabilities)
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e("VolunteerDashboard", "Error: $errorBody")
                    Toast.makeText(this@VolunteerActivity, "Erreur de récupération des disponibilités: $errorBody", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Availability>>, t: Throwable) {
                Log.e("VolunteerDashboard", "Failure: ${t.message}", t)
                Toast.makeText(this@VolunteerActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
