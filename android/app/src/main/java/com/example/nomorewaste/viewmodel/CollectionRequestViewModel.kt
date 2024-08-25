// CollectionRequestViewModel.kt
package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class CollectionRequestViewModel : ViewModel() {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    private val _successMessage = MutableLiveData<String>()
    val successMessage: LiveData<String> get() = _successMessage

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    fun createCollectionRequest(requestData: Map<String, Any>) {
        apiService.createCollectionRequest(requestData).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    _successMessage.postValue("Collection request created successfully!")
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }
}