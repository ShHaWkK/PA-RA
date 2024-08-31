// Path: src/main/java/com/example/nomorewaste/viewmodel/PlanningViewModel.kt
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
import kotlinx.coroutines.withContext
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
        Log.d("PlanningViewModel", "Fetching planning for date: $formattedDate")

        viewModelScope.launch(Dispatchers.IO) {  // Assurez-vous que ce code s'exécute sur un thread de fond
            try {
                val planningItems = ApiClient.fetchPlanningByUserIdAndDate(userId, formattedDate)
                _planningData.postValue(planningItems)  // Utilisez postValue pour les changements de background
            } catch (e: Exception) {
                _errorMessage.postValue("Erreur lors de la récupération des plannings : ${e.message}")
                Log.e("PlanningViewModel", "Erreur lors de la récupération des plannings: ${e.message}")
            }
        }
    }



}
