package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ServiceSchedule
import com.example.nomorewaste.api.ServiceManager
import java.text.SimpleDateFormat
import java.util.*

class PlanningViewModel : ViewModel() {

    private val serviceManager = ServiceManager()

    private val _schedules = MutableLiveData<List<ServiceSchedule>>().apply { value = emptyList() }
    val schedules: LiveData<List<ServiceSchedule>> get() = _schedules

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    fun loadUserSchedule(userId: Int) {
        serviceManager.getUserSchedule(userId) { schedules, throwable ->
            if (schedules != null) {
                _schedules.postValue(schedules)
            } else {
                _schedules.postValue(emptyList())
                _error.postValue(throwable?.message ?: "Erreur inconnue")
            }
        }
    }

    fun loadUserScheduleForDate(userId: Int, date: Date) {
        val formattedDate = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault()).format(date)
        serviceManager.getUserScheduleByDate(userId, formattedDate) { schedules, throwable ->
            if (schedules != null) {
                _schedules.postValue(schedules)
            } else {
                _schedules.postValue(emptyList())
                _error.postValue(throwable?.message ?: "Erreur inconnue")
            }
        }
    }
}
