package com.example.nomorewaste.viewmodel

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import androidx.lifecycle.viewModelScope
import com.example.nomorewaste.api.ApiClient
import com.example.nomorewaste.model.PlanningItem
import kotlinx.coroutines.Dispatchers
import kotlinx.coroutines.launch
import java.text.SimpleDateFormat
import java.util.*

class PlanningViewModel : ViewModel() {

    private val _planningData = MutableLiveData<List<PlanningItem>>()
    val planningData: LiveData<List<PlanningItem>> get() = _planningData

    private val _errorMessage = MutableLiveData<String>()
    val errorMessage: LiveData<String> get() = _errorMessage

    private val dateFormatter = SimpleDateFormat("yyyy-MM-dd", Locale.getDefault())

    fun fetchPlanningByUserIdAndDate(userId: Int, date: Date) {
        val formattedDate = dateFormatter.format(date)
        viewModelScope.launch(Dispatchers.IO) {
            try {
                val planningItems = ApiClient.fetchPlanningByUserIdAndDate(userId, formattedDate)
                val filteredItems = filterPlanningByDate(planningItems, date)

                _planningData.postValue(filteredItems)

                Log.d("PlanningViewModel", "Data fetched for date: $formattedDate with ${filteredItems.size} items")

            } catch (e: Exception) {
                _errorMessage.postValue("Erreur lors de la récupération des plannings : ${e.message}")
                Log.e("PlanningViewModel", "Erreur lors de la récupération des plannings: ${e.message}")
            }
        }
    }

    private fun filterPlanningByDate(planningItems: List<PlanningItem>, selectedDate: Date): List<PlanningItem> {
        val selectedDateStr = dateFormatter.format(selectedDate)
        return planningItems.filter {
            val itemDateStr = dateFormatter.format(parseDate(it.startTime))
            itemDateStr == selectedDateStr
        }
    }

    private fun parseDate(dateString: String): Date {
        return try {
            dateFormatter.parse(dateString) ?: Date()
        } catch (e: Exception) {
            Log.e("PlanningViewModel", "Erreur de parsing de la date : ${e.message}")
            Date()
        }
    }
}
