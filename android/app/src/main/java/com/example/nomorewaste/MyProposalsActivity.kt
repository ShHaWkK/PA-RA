package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.ServiceProposal
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class MyProposalsActivity : AppCompatActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_my_proposals)

        recyclerView = findViewById(R.id.recycler_view_my_proposals)
        recyclerView.layoutManager = LinearLayoutManager(this)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadMyProposals()
    }

    private fun loadMyProposals() {
        val userId = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE).getInt("USER_ID", -1)
        if (userId != -1) {
            apiService.getUserProposals(userId).enqueue(object : Callback<List<ServiceProposal>> {
                override fun onResponse(call: Call<List<ServiceProposal>>, response: Response<List<ServiceProposal>>) {
                    if (response.isSuccessful) {
                        val proposals = response.body()
                        proposals?.let {
                            recyclerView.adapter = ServiceProposalAdapter(it)
                        }
                    } else {
                        Toast.makeText(this@MyProposalsActivity, "Erreur de chargement des propositions", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<List<ServiceProposal>>, t: Throwable) {
                    Toast.makeText(this@MyProposalsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        } else {
            Toast.makeText(this, "Utilisateur non connecté", Toast.LENGTH_SHORT).show()
        }
    }
}
