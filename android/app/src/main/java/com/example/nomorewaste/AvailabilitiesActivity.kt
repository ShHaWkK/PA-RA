package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.*
import com.google.gson.Gson
import com.google.gson.reflect.TypeToken
import okhttp3.ResponseBody
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
        apiService.getAvailabilities(userId).enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    response.body()?.let { responseBody ->
                        val responseStr = responseBody.string()
                        Log.d("AvailabilitiesActivity", "Response JSON: $responseStr")

                        val gson = Gson()
                        val listType = object : TypeToken<List<AvailabilityResponse>>() {}.type

                        try {
                            // Try parsing the response as a list
                            val availabilityList: List<AvailabilityResponse> = gson.fromJson(responseStr, listType) ?: emptyList()

                            if (availabilityList.isNotEmpty()) {
                                recyclerView.adapter = AvailabilityAdapter(availabilityList)
                            } else {
                                Toast.makeText(this@AvailabilitiesActivity, "No availabilities found", Toast.LENGTH_SHORT).show()
                            }
                        } catch (e: Exception) {
                            // If parsing as a list fails, try parsing as a single object
                            try {
                                val singleAvailability: AvailabilityResponse? = gson.fromJson(responseStr, AvailabilityResponse::class.java)
                                if (singleAvailability != null) {
                                    recyclerView.adapter = AvailabilityAdapter(listOf(singleAvailability))
                                } else {
                                    Toast.makeText(this@AvailabilitiesActivity, "No availabilities found", Toast.LENGTH_SHORT).show()
                                }
                            } catch (e: Exception) {
                                Toast.makeText(this@AvailabilitiesActivity, "Failed to parse response", Toast.LENGTH_SHORT).show()
                            }
                        }
                    }
                } else {
                    Toast.makeText(this@AvailabilitiesActivity, "Erreur de récupération des disponibilités", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                Toast.makeText(this@AvailabilitiesActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
