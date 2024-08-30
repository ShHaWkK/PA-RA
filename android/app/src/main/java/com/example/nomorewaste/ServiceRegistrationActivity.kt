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
import com.example.nomorewaste.api.ServiceRegistration
import com.example.nomorewaste.api.ServiceRegistrationRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.SimpleDateFormat
import java.util.Date
import java.util.Locale

class ServiceRegistrationActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var registerButton: Button
    private lateinit var serviceNameTextView: TextView
    private lateinit var serviceDescriptionTextView: TextView
    private lateinit var serviceStartTimeTextView: TextView
    private lateinit var serviceEndTimeTextView: TextView
    private lateinit var serviceCapacityTextView: TextView
    private var serviceId: Int = 0
    private var isAlreadyRegistered: Boolean = false

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service_registration)

        serviceNameTextView = findViewById(R.id.text_service_name)
        serviceDescriptionTextView = findViewById(R.id.text_service_description)
        serviceStartTimeTextView = findViewById(R.id.text_service_start_time)
        serviceEndTimeTextView = findViewById(R.id.text_service_end_time)
        serviceCapacityTextView = findViewById(R.id.text_service_capacity)
        registerButton = findViewById(R.id.button_register_service)

        serviceId = intent.getIntExtra("service_id", 0)
        Log.d("ServiceRegistration", "Service ID received: $serviceId")

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        // Load service details
        loadServiceDetails(serviceId, userId)

        registerButton.setOnClickListener {
            if (userId != -1 && !isAlreadyRegistered) {
                registerForService(serviceId, userId)
            } else if (isAlreadyRegistered) {
                Toast.makeText(this, "Vous êtes déjà inscrit à ce service", Toast.LENGTH_SHORT).show()
            } else {
                Toast.makeText(this, "Erreur de récupération de l'ID de l'utilisateur", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadServiceDetails(serviceId: Int, userId: Int) {
        apiService.getService(serviceId).enqueue(object : Callback<Service> {
            override fun onResponse(call: Call<Service>, response: Response<Service>) {
                if (response.isSuccessful) {
                    val service = response.body()
                    service?.let {
                        val currentDateTime = Date() // Date actuelle
                        val serviceStartDateTime = SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault()).parse(it.startSchedule)

                        if (serviceStartDateTime.before(currentDateTime)) {
                            // Si la date du service est passée
                            Toast.makeText(this@ServiceRegistrationActivity, "Vous ne pouvez pas vous inscrire à un service qui a déjà commencé.", Toast.LENGTH_SHORT).show()
                            registerButton.isEnabled = false // Désactiver le bouton d'inscription
                        } else {
                            // Mise à jour des détails du service
                            serviceNameTextView.text = it.name
                            serviceDescriptionTextView.text = it.description
                            serviceStartTimeTextView.text = "Start Time: ${it.startSchedule}"
                            serviceEndTimeTextView.text = "End Time: ${it.endSchedule}"
                            serviceCapacityTextView.text = "Places restantes : ${it.capacity - it.currentRegistrations}"

                            // Vérifier si l'utilisateur est déjà inscrit à ce service
                            checkUserRegistration(serviceId, userId)
                        }
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



    private fun checkUserRegistration(serviceId: Int, userId: Int) {
        apiService.getUserRegistrations(userId).enqueue(object : Callback<List<ServiceRegistration>> {
            override fun onResponse(call: Call<List<ServiceRegistration>>, response: Response<List<ServiceRegistration>>) {
                if (response.isSuccessful) {
                    val registrations = response.body()
                    registrations?.let {
                        for (registration in it) {
                            if (registration.serviceId == serviceId) {
                                isAlreadyRegistered = true
                                registerButton.text = "Vous êtes déjà inscrit à ce service"
                                registerButton.isEnabled = false
                                break
                            }
                        }
                    }
                } else {
                    Log.e("ServiceRegistration", "Failed to check user registration")
                }
            }

            override fun onFailure(call: Call<List<ServiceRegistration>>, t: Throwable) {
                Log.e("ServiceRegistration", "Failed to check user registration: ${t.message}")
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