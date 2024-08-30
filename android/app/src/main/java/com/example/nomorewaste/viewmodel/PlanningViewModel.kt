// Path: src/main/java/com/example/nomorewaste/viewmodel/PlanningViewModel.kt
package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.*
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response
import java.text.SimpleDateFormat
import java.util.*

class PlanningViewModel : ViewModel() {

    private val _schedules = MutableLiveData<List<Any>>()
    val schedules: LiveData<List<Any>> get() = _schedules

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    fun loadUserSchedule(userId: Int) {
        val call = RetrofitInstance.api.getUserSchedule(userId)
        call.enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    _schedules.value = response.body()?.toList()
                } else {
                    _error.value = "Failed to load schedules"
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                _error.value = t.message
            }
        })
    }

    fun loadUserScheduleForDate(userId: Int, date: Date) {
        val formattedDate = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(date)
        val call = RetrofitInstance.api.getUserScheduleByDate(userId, formattedDate)
        call.enqueue(object : Callback<PlanningResponse> {
            override fun onResponse(call: Call<PlanningResponse>, response: Response<PlanningResponse>) {
                if (response.isSuccessful) {
                    _schedules.value = response.body()?.toList()
                } else {
                    _error.value = "Failed to load schedules for date"
                }
            }

            override fun onFailure(call: Call<PlanningResponse>, t: Throwable) {
                _error.value = t.message
            }
        })
    }
}
