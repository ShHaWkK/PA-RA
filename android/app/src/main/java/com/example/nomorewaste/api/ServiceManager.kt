package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceManager {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    fun getPlanningByDate(userId: Int, date: String, callback: (PlanningResponse?, Throwable?) -> Unit) {
        val call = apiService.getPlanningByDate(userId, date, date)
        call.enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    callback(response.body(), null)
                } else {
                    callback(null, Throwable("Error: ${response.message()}"))
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                callback(null, t)
            }
        })
    }
}
