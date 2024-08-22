package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ServiceSchedule
import com.example.nomorewaste.api.ServiceManager

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
                _schedules.postValue(emptyList()) // Provide a default empty list
                _error.postValue(throwable?.message ?: "Unknown error")
            }
        }
    }
}
