package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServiceManager {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    fun getUserSchedule(userId: Int, callback: (List<ServiceSchedule>?, Throwable?) -> Unit) {
        apiService.getUserSchedule(userId).enqueue(object : Callback<List<ServiceSchedule>> {
            override fun onResponse(call: Call<List<ServiceSchedule>>, response: Response<List<ServiceSchedule>>) {
                if (response.isSuccessful) {
                    callback(response.body(), null)
                } else {
                    callback(null, Throwable(response.message()))
                }
            }

            override fun onFailure(call: Call<List<ServiceSchedule>>, t: Throwable) {
                callback(null, t)
            }
        })
    }
}
