// Path: src/main/java/com/example/nomorewaste/api/ServiceManager.kt
package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceManager {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    // Fonction pour obtenir la planification d'un utilisateur
    fun getPlanning(userId: Int, callback: (List<Any>?, Throwable?) -> Unit) {
        val call = apiService.getPlanning(userId)
        call.enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(
                call: Call<PlanningResponse>,
                response: Response<PlanningResponse>
            ) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        val planningItems = mutableListOf<Any>()
                        planningItems.addAll(it.routes)
                        planningItems.addAll(it.collections)
                        planningItems.addAll(it.deliveries)
                        planningItems.addAll(it.services)
                        callback(planningItems, null)
                    } ?: callback(null, Throwable("Response body is null"))
                } else {
                    callback(null, Throwable("Error: ${response.message()}"))
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                callback(null, t)
            }
        })
    }

    // Fonction pour obtenir la planification d'un utilisateur par date
    fun getPlanningByDate(userId: Int, startDate: String, endDate: String, callback: (List<Any>?, Throwable?) -> Unit) {
        val call = apiService.getPlanningByDate(userId, startDate, endDate)
        call.enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(
                call: Call<PlanningResponse>,
                response: Response<PlanningResponse>
            ) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        val planningItems = mutableListOf<Any>()
                        planningItems.addAll(it.routes)
                        planningItems.addAll(it.collections)
                        planningItems.addAll(it.deliveries)
                        planningItems.addAll(it.services)
                        callback(planningItems, null)
                    } ?: callback(null, Throwable("Response body is null"))
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
