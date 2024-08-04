package com.example.nomorewaste

import android.app.TimePickerDialog
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Availability
import com.example.nomorewaste.api.RegisterVolunteerRequest
import com.example.nomorewaste.api.RetrofitClient
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

    private lateinit var mondayStartButton: Button
    private lateinit var mondayEndButton: Button
    private lateinit var tuesdayStartButton: Button
    private lateinit var tuesdayEndButton: Button
    private lateinit var wednesdayStartButton: Button
    private lateinit var wednesdayEndButton: Button
    private lateinit var thursdayStartButton: Button
    private lateinit var thursdayEndButton: Button
    private lateinit var fridayStartButton: Button
    private lateinit var fridayEndButton: Button
    private lateinit var saturdayStartButton: Button
    private lateinit var saturdayEndButton: Button
    private lateinit var sundayStartButton: Button
    private lateinit var sundayEndButton: Button

    private lateinit var apiService: ApiService
    private val availabilities = mutableListOf<Availability>()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_volunteer)

        initViews()
        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        setupTimePickerListeners()
        buttonRegister.setOnClickListener { registerVolunteer() }
    }

    private fun initViews() {
        editFirstName = findViewById(R.id.first_name)
        editLastName = findViewById(R.id.last_name)
        editEmail = findViewById(R.id.email)
        editPhoneNumber = findViewById(R.id.phone_number)
        editPassword = findViewById(R.id.password)
        buttonRegister = findViewById(R.id.register_button)

        mondayStartButton = findViewById(R.id.monday_start_button)
        mondayEndButton = findViewById(R.id.monday_end_button)
        tuesdayStartButton = findViewById(R.id.tuesday_start_button)
        tuesdayEndButton = findViewById(R.id.tuesday_end_button)
        wednesdayStartButton = findViewById(R.id.wednesday_start_button)
        wednesdayEndButton = findViewById(R.id.wednesday_end_button)
        thursdayStartButton = findViewById(R.id.thursday_start_button)
        thursdayEndButton = findViewById(R.id.thursday_end_button)
        fridayStartButton = findViewById(R.id.friday_start_button)
        fridayEndButton = findViewById(R.id.friday_end_button)
        saturdayStartButton = findViewById(R.id.saturday_start_button)
        saturdayEndButton = findViewById(R.id.saturday_end_button)
        sundayStartButton = findViewById(R.id.sunday_start_button)
        sundayEndButton = findViewById(R.id.sunday_end_button)
    }

    private fun setupTimePickerListeners() {
        val timePickerListener = { button: Button, day: String, isStart: Boolean ->
            val calendar = Calendar.getInstance()
            val hour = calendar.get(Calendar.HOUR_OF_DAY)
            val minute = calendar.get(Calendar.MINUTE)
            TimePickerDialog(this, { _, selectedHour, selectedMinute ->
                val time = String.format("%02d:%02d", selectedHour, selectedMinute)
                button.text = time
                updateAvailability(day, time, isStart)
            }, hour, minute, true).show()
        }

        mondayStartButton.setOnClickListener { timePickerListener(mondayStartButton, "Monday", true) }
        mondayEndButton.setOnClickListener { timePickerListener(mondayEndButton, "Monday", false) }
        tuesdayStartButton.setOnClickListener { timePickerListener(tuesdayStartButton, "Tuesday", true) }
        tuesdayEndButton.setOnClickListener { timePickerListener(tuesdayEndButton, "Tuesday", false) }
        wednesdayStartButton.setOnClickListener { timePickerListener(wednesdayStartButton, "Wednesday", true) }
        wednesdayEndButton.setOnClickListener { timePickerListener(wednesdayEndButton, "Wednesday", false) }
        thursdayStartButton.setOnClickListener { timePickerListener(thursdayStartButton, "Thursday", true) }
        thursdayEndButton.setOnClickListener { timePickerListener(thursdayEndButton, "Thursday", false) }
        fridayStartButton.setOnClickListener { timePickerListener(fridayStartButton, "Friday", true) }
        fridayEndButton.setOnClickListener { timePickerListener(fridayEndButton, "Friday", false) }
        saturdayStartButton.setOnClickListener { timePickerListener(saturdayStartButton, "Saturday", true) }
        saturdayEndButton.setOnClickListener { timePickerListener(saturdayEndButton, "Saturday", false) }
        sundayStartButton.setOnClickListener { timePickerListener(sundayStartButton, "Sunday", true) }
        sundayEndButton.setOnClickListener { timePickerListener(sundayEndButton, "Sunday", false) }
    }

    private fun registerVolunteer() {
        val firstName = editFirstName.text.toString()
        val lastName = editLastName.text.toString()
        val email = editEmail.text.toString()
        val phoneNumber = editPhoneNumber.text.toString()
        val password = editPassword.text.toString()

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || phoneNumber.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            return
        }

        val registerRequest = RegisterVolunteerRequest(
            first_name = firstName,
            last_name = lastName,
            email = email,
            phone_number = phoneNumber,
            password = password,
            availabilities = availabilities
        )

        apiService.registerVolunteer(registerRequest).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@RegisterVolunteerActivity, "Inscription réussie", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@RegisterVolunteerActivity, "Erreur lors de l'inscription", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@RegisterVolunteerActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateAvailability(day: String, time: String, isStart: Boolean) {
        val availabilityIndex = availabilities.indexOfFirst { it.day_of_week == day }
        if (availabilityIndex != -1) {
            val availability = availabilities[availabilityIndex]
            val updatedAvailability = availability.copy(
                start_time = if (isStart) time else availability.start_time,
                end_time = if (!isStart) time else availability.end_time
            )
            availabilities[availabilityIndex] = updatedAvailability
        } else {
            val newAvailability = Availability(
                day_of_week = day,
                start_time = if (isStart) time else "",
                end_time = if (isStart) "" else time
            )
            availabilities.add(newAvailability)
        }
    }
}
