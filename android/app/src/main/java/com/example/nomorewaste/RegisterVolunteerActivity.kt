package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.CheckBox
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RegisterVolunteerRequest
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class RegisterVolunteerActivity : AppCompatActivity() {
    private lateinit var firstNameEditText: EditText
    private lateinit var lastNameEditText: EditText
    private lateinit var emailEditText: EditText
    private lateinit var phoneNumberEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var registerButton: Button
    private var apiService: ApiService? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_volunteer)

        firstNameEditText = findViewById(R.id.first_name)
        lastNameEditText = findViewById(R.id.last_name)
        emailEditText = findViewById(R.id.email)
        phoneNumberEditText = findViewById(R.id.phone_number)
        passwordEditText = findViewById(R.id.password)
        registerButton = findViewById(R.id.register_button)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        registerButton.setOnClickListener { register() }
    }

    private fun register() {
        val firstName = firstNameEditText.text.toString()
        val lastName = lastNameEditText.text.toString()
        val email = emailEditText.text.toString()
        val phoneNumber = phoneNumberEditText.text.toString()
        val password = passwordEditText.text.toString()
        val skills = mutableListOf<String>()

        val skillDriver: CheckBox = findViewById(R.id.skill_driver)
        val skillCook: CheckBox = findViewById(R.id.skill_cook)
        val skillPlumber: CheckBox = findViewById(R.id.skill_plumber)
        val skillElectrician: CheckBox = findViewById(R.id.skill_electrician)
        val skillTeacher: CheckBox = findViewById(R.id.skill_teacher)
        val skillGardener: CheckBox = findViewById(R.id.skill_gardener)

        if (skillDriver.isChecked) skills.add("driver")
        if (skillCook.isChecked) skills.add("cook")
        if (skillPlumber.isChecked) skills.add("plumber")
        if (skillElectrician.isChecked) skills.add("electrician")
        if (skillTeacher.isChecked) skills.add("teacher")
        if (skillGardener.isChecked) skills.add("gardener")

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || phoneNumber.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Tous les champs sont obligatoires", Toast.LENGTH_SHORT).show()
            return
        }

        val request = RegisterVolunteerRequest(firstName, lastName, email, phoneNumber, password, skills)
        apiService?.registerVolunteer(request)?.enqueue(object : Callback<Void?> {
            override fun onResponse(call: Call<Void?>, response: Response<Void?>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@RegisterVolunteerActivity, "Inscription réussie. Votre compte est en attente de validation par les administrateurs.", Toast.LENGTH_LONG).show()
                    finish()
                } else {
                    Toast.makeText(this@RegisterVolunteerActivity, "Échec de l'inscription", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void?>, t: Throwable) {
                Log.e("RegisterVolunteerActivity", "onFailure: ", t)
                Toast.makeText(this@RegisterVolunteerActivity, "Une erreur s'est produite", Toast.LENGTH_SHORT).show()
            }
        })
    }
}