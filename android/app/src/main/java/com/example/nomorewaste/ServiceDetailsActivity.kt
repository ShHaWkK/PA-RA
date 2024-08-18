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
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceDetailsActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var serviceName: TextView
    private lateinit var serviceDescription: TextView
    private lateinit var serviceCapacity: TextView
    private lateinit var unsubscribeButton: Button
    private var serviceId: Int = 0
    private var registrationId: Int = 0

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service_details)

        serviceName = findViewById(R.id.service_name)
        serviceDescription = findViewById(R.id.service_description)
        serviceCapacity = findViewById(R.id.service_capacity)
        unsubscribeButton = findViewById(R.id.button_unsubscribe)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        serviceId = intent.getIntExtra("service_id", 0)
        registrationId = intent.getIntExtra("registration_id", 0)  // Ensure this is passed correctly
        loadServiceDetails()

        unsubscribeButton.setOnClickListener {
            unsubscribeFromService(registrationId)
        }
    }

    private fun loadServiceDetails() {
        apiService.getServiceById(serviceId).enqueue(object : Callback<Service> {
            override fun onResponse(call: Call<Service>, response: Response<Service>) {
                if (response.isSuccessful) {
                    val service = response.body()
                    service?.let {
                        serviceName.text = it.name
                        serviceDescription.text = it.description
                        serviceCapacity.text = "Places restantes: ${it.remainingCapacity}" // Display remaining capacity
                    }
                } else {
                    Toast.makeText(this@ServiceDetailsActivity, "Erreur de chargement des détails du service", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Service>, t: Throwable) {
                Log.e("ServiceDetails", "Erreur lors du chargement des détails du service : ${t.message}")
            }
        })
    }

    private fun unsubscribeFromService(registrationId: Int) {
        if (registrationId != 0) {
            apiService.unsubscribeFromService(registrationId).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@ServiceDetailsActivity, "Désinscription réussie", Toast.LENGTH_SHORT).show()
                        finish() // Close activity after successful unsubscription
                    } else {
                        Toast.makeText(this@ServiceDetailsActivity, "Erreur de désinscription", Toast.LENGTH_SHORT).show()
                        Log.e("Unsubscribe", "Failed to unsubscribe: ${response.errorBody()?.string()}")
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    Toast.makeText(this@ServiceDetailsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                    Log.e("Unsubscribe", "Network failure: ${t.message}", t)
                }
            })
        } else {
            Toast.makeText(this, "Impossible de trouver l'inscription", Toast.LENGTH_SHORT).show()
        }
    }
}
