package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Service
import com.example.nomorewaste.api.ServiceAdapter
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceActivity : AppCompatActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var apiService: ApiService
    private lateinit var proposeServiceButton: Button
    private lateinit var myRegistrationsButton: Button
    private lateinit var myProposalsButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service)

        recyclerView = findViewById(R.id.recycler_view_services)
        proposeServiceButton = findViewById(R.id.button_propose_service)
        myRegistrationsButton = findViewById(R.id.button_my_registrations)
        myProposalsButton = findViewById(R.id.button_my_proposals)

        recyclerView.layoutManager = LinearLayoutManager(this)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        proposeServiceButton.setOnClickListener {
            val intent = Intent(this, ServicePropositionActivity::class.java)
            startActivity(intent)
        }

        myRegistrationsButton.setOnClickListener {
            val intent = Intent(this, MyRegistrationsActivity::class.java)
            startActivity(intent)
        }

        myProposalsButton.setOnClickListener {
            val intent = Intent(this, MyProposalsActivity::class.java)
            startActivity(intent)
        }

        loadServices()
    }

    private fun loadServices() {
        apiService.getServices().enqueue(object : Callback<List<Service>> {
            override fun onResponse(call: Call<List<Service>>, response: Response<List<Service>>) {
                if (response.isSuccessful) {
                    val services = response.body()
                    services?.let {
                        recyclerView.adapter = ServiceAdapter(it) { service ->
                            val intent = Intent(this@ServiceActivity, ServiceRegistrationActivity::class.java)
                            intent.putExtra("service_id", service.id)
                            startActivity(intent)
                        }
                        recyclerView.adapter?.notifyDataSetChanged()
                    }
                } else {
                    Toast.makeText(this@ServiceActivity, "Erreur de chargement des services", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Service>>, t: Throwable) {
                Toast.makeText(this@ServiceActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
