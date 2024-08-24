package com.example.nomorewaste.viewmodel

import android.util.Log
import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.ProductNotification
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProductNotificationViewModel : ViewModel() {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    private val _productNotifications = MutableLiveData<List<ProductNotification>>()
    val productNotifications: LiveData<List<ProductNotification>> get() = _productNotifications

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    fun loadAllProductNotifications() {
        apiService.getAllProductNotifications().enqueue(object : Callback<List<ProductNotification>> {
            override fun onResponse(call: Call<List<ProductNotification>>, response: Response<List<ProductNotification>>) {
                if (response.isSuccessful) {
                    val rawJson = response.body().toString() // Log raw JSON response here
                    Log.d("Raw JSON Response", rawJson)
                    _productNotifications.postValue(response.body())
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<List<ProductNotification>>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })


    }

    fun updateProductNotification(id: Int, updateData: Map<String, Any>) {
        apiService.updateProductNotification(id, updateData).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    loadAllProductNotifications() // Refresh the list after update
                } else {
                    _error.postValue("Error updating notification: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }
}