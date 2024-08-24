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
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceDetailsActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var serviceName: TextView
    private lateinit var serviceDescription: TextView
    private lateinit var serviceStartTime: TextView
    private lateinit var serviceEndTime: TextView
    private lateinit var serviceCapacity: TextView
    private lateinit var unsubscribeButton: Button
    private var serviceId: Int = 0
    private var registrationId: Int = 0
    private var userId: Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service_details)

        serviceName = findViewById(R.id.service_name)
        serviceDescription = findViewById(R.id.service_description)
        serviceStartTime = findViewById(R.id.service_start_time)
        serviceEndTime = findViewById(R.id.service_end_time)
        serviceCapacity = findViewById(R.id.service_capacity)
        unsubscribeButton = findViewById(R.id.button_unsubscribe)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        serviceId = intent.getIntExtra("service_id", 0)
        registrationId = intent.getIntExtra("registration_id", 0)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        userId = sharedPreferences.getInt("USER_ID", -1)

        Log.d("ServiceDetails", "Service ID: $serviceId, Registration ID: $registrationId, User ID: $userId")

        if (registrationId == 0) {
            fetchRegistrationId()
        } else {
            loadServiceDetails()
        }

        unsubscribeButton.setOnClickListener {
            unsubscribeFromService()
        }
    }

    private fun fetchRegistrationId() {
        apiService.getUserRegistrations(userId).enqueue(object : Callback<List<ServiceRegistration>> {
            override fun onResponse(call: Call<List<ServiceRegistration>>, response: Response<List<ServiceRegistration>>) {
                if (response.isSuccessful) {
                    val registrations = response.body()
                    Log.d("ServiceDetails", "Received registrations: $registrations")
                    registrations?.let {
                        val registration = it.find { it.serviceId == serviceId }  // Utilisez le nouveau calcul pour serviceId
                        registration?.let { reg ->
                            registrationId = reg.id
                            Log.d("ServiceDetails", "Found Registration ID: $registrationId")
                            loadServiceDetails()
                        } ?: run {
                            Log.d("ServiceDetails", "No registration found for service ID: $serviceId")
                            Toast.makeText(this@ServiceDetailsActivity, "Aucune inscription trouvée pour ce service", Toast.LENGTH_SHORT).show()
                        }
                    } ?: run {
                        Log.d("ServiceDetails", "No registrations found for user ID: $userId")
                        Toast.makeText(this@ServiceDetailsActivity, "Erreur lors de la récupération des inscriptions", Toast.LENGTH_SHORT).show()
                    }
                } else {
                    Log.e("ServiceDetails", "Erreur lors de la récupération des inscriptions de l'utilisateur, code: ${response.code()}")
                    Toast.makeText(this@ServiceDetailsActivity, "Erreur lors de la récupération des inscriptions", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<ServiceRegistration>>, t: Throwable) {
                Log.e("ServiceDetails", "Échec de la connexion : ${t.message}")
                Toast.makeText(this@ServiceDetailsActivity, "Erreur de connexion", Toast.LENGTH_SHORT).show()
            }
        })
    }


    private fun loadServiceDetails() {
        apiService.getServiceById(serviceId).enqueue(object : Callback<Service> {
            override fun onResponse(call: Call<Service>, response: Response<Service>) {
                if (response.isSuccessful) {
                    val service = response.body()
                    service?.let {
                        serviceName.text = it.name
                        serviceDescription.text = it.description
                        serviceStartTime.text = "Start Time: ${it.startSchedule}"
                        serviceEndTime.text = "End Time: ${it.endSchedule}"
                        serviceCapacity.text = "Place Restantes : ${it.remainingCapacity}"
                    }
                } else {
                    Log.e("ServiceDetails", "Erreur de chargement des détails du service, code: ${response.code()}")
                    Toast.makeText(this@ServiceDetailsActivity, "Erreur de chargement des détails du service", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Service>, t: Throwable) {
                Log.e("ServiceDetails", "Erreur lors du chargement des détails du service : ${t.message}")
                Toast.makeText(this@ServiceDetailsActivity, "Erreur de connexion", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun unsubscribeFromService() {
        if (registrationId != 0) {
            apiService.unsubscribeFromService(registrationId).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@ServiceDetailsActivity, "Désinscription réussie", Toast.LENGTH_SHORT).show()
                        finish() // Close activity after successful unsubscription
                    } else {
                        Log.e("Unsubscribe", "Failed to unsubscribe: ${response.errorBody()?.string()}")
                        Toast.makeText(this@ServiceDetailsActivity, "Erreur de désinscription. Code: ${response.code()}", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    Log.e("Unsubscribe", "Network failure: ${t.message}", t)
                    Toast.makeText(this@ServiceDetailsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        } else {
            Toast.makeText(this, "Impossible de trouver l'inscription", Toast.LENGTH_SHORT).show()
            Log.e("Unsubscribe", "Registration ID is zero, cannot unsubscribe.")
        }
    }
}

