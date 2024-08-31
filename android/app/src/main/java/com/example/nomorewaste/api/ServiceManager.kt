// Path: src/main/java/com/example/nomorewaste/api/ServiceManager.kt
package com.example.nomorewaste.api

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.SimpleDateFormat
import java.util.*

class ServiceManager(private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)) {

    fun getPlanningByDate(date: Date): LiveData<Result<PlanningResponse>> {
        val result = MutableLiveData<Result<PlanningResponse>>()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate = dateFormat.format(date)

        apiService.getPlanningByDate(formattedDate).enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        result.postValue(Result.success(it))
                    } ?: run {
                        result.postValue(Result.failure(Throwable("Aucune donnée trouvée pour cette date")))
                    }
                } else {
                    result.postValue(Result.failure(Throwable("Erreur de récupération des plannings: ${response.code()}")))
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                result.postValue(Result.failure(Throwable("Erreur de connexion: ${t.message}")))
            }
        })

        return result
    }

    fun getPlanningByUserIdAndDate(userId: Int, date: Date): LiveData<Result<PlanningResponse>> {
        val result = MutableLiveData<Result<PlanningResponse>>()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedDate = dateFormat.format(date)

        apiService.getPlanningByUserIdAndDate(userId, formattedDate).enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        result.postValue(Result.success(it))
                    } ?: run {
                        result.postValue(Result.failure(Throwable("Aucune donnée trouvée pour cette date")))
                    }
                } else {
                    result.postValue(Result.failure(Throwable("Erreur de récupération des plannings: ${response.code()}")))
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                result.postValue(Result.failure(Throwable("Erreur de connexion: ${t.message}")))
            }
        })

        return result
    }

    fun getPlanningByUserIdAndDateRange(userId: Int, startDate: Date, endDate: Date): LiveData<Result<PlanningResponse>> {
        val result = MutableLiveData<Result<PlanningResponse>>()
        val dateFormat = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())
        val formattedStartDate = dateFormat.format(startDate)
        val formattedEndDate = dateFormat.format(endDate)

        apiService.getPlanningByUserIdAndDateRange(userId, formattedStartDate, formattedEndDate).enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        result.postValue(Result.success(it))
                    } ?: run {
                        result.postValue(Result.failure(Throwable("Aucune donnée trouvée pour cette période")))
                    }
                } else {
                    result.postValue(Result.failure(Throwable("Erreur de récupération des plannings: ${response.code()}")))
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                result.postValue(Result.failure(Throwable("Erreur de connexion: ${t.message}")))
            }
        })

        return result
    }
}
