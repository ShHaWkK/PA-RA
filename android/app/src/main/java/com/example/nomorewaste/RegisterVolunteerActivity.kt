package com.example.nomorewaste

import android.app.TimePickerDialog
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.CheckBox
import android.widget.EditText
import android.widget.LinearLayout
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.*
import com.google.gson.Gson
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.util.*

class RegisterVolunteerActivity : AppCompatActivity() {

    private lateinit var editFirstName: EditText
    private lateinit var editLastName: EditText
    private lateinit var editEmail: EditText
    private lateinit var editPhoneNumber: EditText
    private lateinit var editPassword: EditText
    private lateinit var buttonRegister: Button
    private lateinit var skillsContainer: LinearLayout
    private lateinit var availabilityRecyclerView: RecyclerView

    private lateinit var apiService: ApiService
    private val availabilities = mutableListOf<AvailabilityRequest>()
    private val selectedSkills = mutableListOf<Int>()
    private lateinit var availabilityAdapter: RegisterVolunteerAvailabilityAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_volunteer)

        initViews()
        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        loadSkills()
        setupRecyclerView()
        buttonRegister.setOnClickListener { registerVolunteer() }
    }

    private fun initViews() {
        editFirstName = findViewById(R.id.first_name)
        editLastName = findViewById(R.id.last_name)
        editEmail = findViewById(R.id.email)
        editPhoneNumber = findViewById(R.id.phone_number)
        editPassword = findViewById(R.id.password)
        buttonRegister = findViewById(R.id.register_button)
        skillsContainer = findViewById(R.id.skills_container)
        availabilityRecyclerView = findViewById(R.id.availability_recycler_view)

        val days = listOf(
            "Monday" to Pair(R.id.monday_start_button, R.id.monday_end_button),
            "Tuesday" to Pair(R.id.tuesday_start_button, R.id.tuesday_end_button),
            "Wednesday" to Pair(R.id.wednesday_start_button, R.id.wednesday_end_button),
            "Thursday" to Pair(R.id.thursday_start_button, R.id.thursday_end_button),
            "Friday" to Pair(R.id.friday_start_button, R.id.friday_end_button),
            "Saturday" to Pair(R.id.saturday_start_button, R.id.saturday_end_button),
            "Sunday" to Pair(R.id.sunday_start_button, R.id.sunday_end_button)
        )

        for ((day, buttonIds) in days) {
            findViewById<Button>(buttonIds.first).setOnClickListener { setupTimePicker(day, true) }
            findViewById<Button>(buttonIds.second).setOnClickListener { setupTimePicker(day, false) }
        }
    }

    private fun setupRecyclerView() {
        availabilityAdapter = RegisterVolunteerAvailabilityAdapter(availabilities)
        availabilityRecyclerView.layoutManager = LinearLayoutManager(this)
        availabilityRecyclerView.adapter = availabilityAdapter
    }

    private fun loadSkills() {
        apiService.getSkills().enqueue(object : Callback<List<Skill>> {
            override fun onResponse(call: Call<List<Skill>>, response: Response<List<Skill>>) {
                if (response.isSuccessful && response.body() != null) {
                    populateSkills(response.body()!!)
                } else {
                    Toast.makeText(this@RegisterVolunteerActivity, "Erreur lors du chargement des compétences", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Skill>>, t: Throwable) {
                Toast.makeText(this@RegisterVolunteerActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun populateSkills(skills: List<Skill>) {
        skills.forEach { skill ->
            val checkBox = CheckBox(this)
            checkBox.text = skill.name
            checkBox.setOnCheckedChangeListener { _, isChecked ->
                if (isChecked) {
                    selectedSkills.add(skill.id)
                } else {
                    selectedSkills.remove(skill.id)
                }
            }
            skillsContainer.addView(checkBox)
        }
    }

    private fun setupTimePicker(day: String, isStart: Boolean) {
        val calendar = Calendar.getInstance()
        val hour = calendar.get(Calendar.HOUR_OF_DAY)
        val minute = calendar.get(Calendar.MINUTE)

        TimePickerDialog(this, { _, selectedHour, selectedMinute ->
            val time = String.format("%02d:%02d", selectedHour, selectedMinute)
            updateAvailability(day, time, isStart)
        }, hour, minute, true).show()
    }

    private fun registerVolunteer() {
        val firstName = editFirstName.text.toString()
        val lastName = editLastName.text.toString()
        val email = editEmail.text.toString()
        val phoneNumber = editPhoneNumber.text.toString()
        val password = editPassword.text.toString()

        for (availability in availabilities) {
            if (availability.dayOfWeek.isEmpty() ||
                availability.startTime.isEmpty() ||
                availability.endTime.isEmpty()) {
                Toast.makeText(this, "Please provide complete availability information", Toast.LENGTH_SHORT).show()
                return
            }
        }

        val registerRequest = RegisterVolunteerRequest(
            firstName = firstName,
            lastName = lastName,
            email = email,
            phoneNumber = phoneNumber,
            password = password,
            skills = selectedSkills,
            availabilities = availabilities
        )

        val json = Gson().toJson(registerRequest)
        Log.d("RegisterVolunteer", "JSON Payload: $json")

        apiService.registerVolunteer(registerRequest).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@RegisterVolunteerActivity, "Inscription réussie", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    val errorBody = response.errorBody()?.string()
                    Log.e("RegisterVolunteer", "Error response: $errorBody")
                    Toast.makeText(this@RegisterVolunteerActivity, "Erreur lors de l'inscription: $errorBody", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@RegisterVolunteerActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateAvailability(day: String, time: String, isStart: Boolean) {
        val availabilityIndex = availabilities.indexOfFirst { it.dayOfWeek == day }
        if (availabilityIndex != -1) {
            val availability = availabilities[availabilityIndex]
            val updatedAvailability = availability.copy(
                startTime = if (isStart) time else availability.startTime,
                endTime = if (!isStart) time else availability.endTime
            )
            availabilities[availabilityIndex] = updatedAvailability
        } else {
            val newAvailability = AvailabilityRequest(
                dayOfWeek = day,
                startTime = if (isStart) time else "",
                endTime = if (!isStart) time else ""
            )
            availabilities.add(newAvailability)
        }
        availabilityAdapter.notifyDataSetChanged()
    }
}
