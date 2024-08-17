package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.TextView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Service
import com.example.nomorewaste.api.ServiceRegistrationRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceRegistrationActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var registerButton: Button
    private lateinit var serviceNameTextView: TextView
    private lateinit var serviceDescriptionTextView: TextView
    private lateinit var serviceCapacityTextView: TextView
    private var serviceId: Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service_registration)

        serviceNameTextView = findViewById(R.id.text_service_name)
        serviceDescriptionTextView = findViewById(R.id.text_service_description)
        serviceCapacityTextView = findViewById(R.id.text_service_capacity)

        serviceId = intent.getIntExtra("service_id", 0)

        Log.d("ServiceRegistration", "Service ID received: $serviceId")

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)
        registerButton = findViewById(R.id.button_register_service)

        // Load service details
        loadServiceDetails(serviceId)

        registerButton.setOnClickListener {
            if (userId != -1) {
                registerForService(serviceId, userId)
            } else {
                Toast.makeText(this, "Erreur de récupération de l'ID de l'utilisateur", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadServiceDetails(serviceId: Int) {
        apiService.getService(serviceId).enqueue(object : Callback<Service> {
            override fun onResponse(call: Call<Service>, response: Response<Service>) {
                if (response.isSuccessful) {
                    val service = response.body()
                    service?.let {
                        Log.d("ServiceRegistration", "Service details loaded: $it")
                        serviceNameTextView.text = it.name
                        serviceDescriptionTextView.text = it.description
                        serviceCapacityTextView.text = "Capacité : ${it.capacity}"
                    } ?: run {
                        Log.e("ServiceRegistration", "Service is null")
                    }
                } else {
                    Toast.makeText(this@ServiceRegistrationActivity, "Erreur de chargement des détails du service", Toast.LENGTH_SHORT).show()
                    Log.e("ServiceRegistration", "Response failed: ${response.errorBody()?.string()}")
                }
            }

            override fun onFailure(call: Call<Service>, t: Throwable) {
                Toast.makeText(this@ServiceRegistrationActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                Log.e("ServiceRegistration", "Network failure: ${t.message}", t)
            }
        })
    }

    private fun registerForService(serviceId: Int, userId: Int) {
        val request = ServiceRegistrationRequest(serviceId, userId)
        apiService.registerForService(request).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ServiceRegistrationActivity, "Inscription réussie", Toast.LENGTH_SHORT).show()
                    finish() // Close activity after successful registration
                } else {
                    Toast.makeText(this@ServiceRegistrationActivity, "Erreur d'inscription", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@ServiceRegistrationActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
