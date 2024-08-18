package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Service
import com.example.nomorewaste.api.ServiceRegistration
import com.example.nomorewaste.api.ServiceRegistrationAdapter
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class MyRegistrationsActivity : AppCompatActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var apiService: ApiService
    private val services = mutableListOf<Service>()
    private lateinit var adapter: ServiceRegistrationAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_registrations)

        recyclerView = findViewById(R.id.recycler_view_my_registrations)
        recyclerView.layoutManager = LinearLayoutManager(this)

        adapter = ServiceRegistrationAdapter(services) { service ->
            val intent = Intent(this@MyRegistrationsActivity, ServiceDetailsActivity::class.java)
            intent.putExtra("service_id", service.id)
            startActivity(intent)
        }
        recyclerView.adapter = adapter

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadMyRegistrations()
    }

    private fun loadMyRegistrations() {
        val userId = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE).getInt("USER_ID", -1)
        if (userId != -1) {
            apiService.getUserRegistrations(userId).enqueue(object : Callback<List<ServiceRegistration>> {
                override fun onResponse(call: Call<List<ServiceRegistration>>, response: Response<List<ServiceRegistration>>) {
                    if (response.isSuccessful) {
                        val registrations = response.body()
                        registrations?.let {
                            for (registration in it) {
                                loadServiceDetails(registration)
                            }
                        }
                    } else {
                        Toast.makeText(this@MyRegistrationsActivity, "Erreur de chargement des inscriptions", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<List<ServiceRegistration>>, t: Throwable) {
                    Toast.makeText(this@MyRegistrationsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        } else {
            Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
        }
    }

    private fun loadServiceDetails(registration: ServiceRegistration) {
        apiService.getServiceById(registration.serviceId).enqueue(object : Callback<Service> {
            override fun onResponse(call: Call<Service>, response: Response<Service>) {
                if (response.isSuccessful) {
                    val service = response.body()
                    service?.let {
                        services.add(it)
                        adapter.notifyDataSetChanged()
                    }
                } else {
                    Log.e("MyRegistrations", "Failed to load service details")
                }
            }

            override fun onFailure(call: Call<Service>, t: Throwable) {
                Log.e("MyRegistrations", "Error loading service details: ${t.message}")
            }
        })
    }
}
