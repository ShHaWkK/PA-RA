package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Availability
import com.example.nomorewaste.api.AvailabilityAdapter
import com.example.nomorewaste.api.RetrofitClient
import com.google.gson.Gson
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class AvailabilitiesActivity : AppCompatActivity() {

    private lateinit var recyclerView: RecyclerView
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_availabilities)

        recyclerView = findViewById(R.id.recycler_view)
        recyclerView.layoutManager = LinearLayoutManager(this)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        if (userId != -1) {
            getAvailabilities(userId)
        } else {
            Toast.makeText(this, "Erreur de récupération de l'ID de l'utilisateur", Toast.LENGTH_SHORT).show()
            finish()
        }
    }

    private fun getAvailabilities(userId: Int) {
        apiService.getAvailabilities(userId).enqueue(object : Callback<Any> {
            override fun onResponse(call: Call<Any>, response: Response<Any>) {
                if (response.isSuccessful) {
                    val responseBody = response.body()
                    if (responseBody is List<*>) {
                        val availabilityList = responseBody.filterIsInstance<Availability>()
                        Log.d("AvailabilitiesActivity", "Availabilities List: $availabilityList")
                        recyclerView.adapter = AvailabilityAdapter(availabilityList)
                    } else if (responseBody is Map<*, *>) {
                        val singleAvailability = Gson().fromJson(Gson().toJson(responseBody), Availability::class.java)
                        Log.d("AvailabilitiesActivity", "Single Availability: $singleAvailability")
                        recyclerView.adapter = AvailabilityAdapter(listOf(singleAvailability))
                    } else {
                        Toast.makeText(this@AvailabilitiesActivity, "Unexpected response format", Toast.LENGTH_SHORT).show()
                    }
                } else {
                    Toast.makeText(this@AvailabilitiesActivity, "Erreur de récupération des disponibilités", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Any>, t: Throwable) {
                Toast.makeText(this@AvailabilitiesActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

}
