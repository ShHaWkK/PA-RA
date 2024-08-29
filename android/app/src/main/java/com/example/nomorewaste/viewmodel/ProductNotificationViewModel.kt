package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.ProductNotification
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.UpdateProductNotificationRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProductNotificationViewModel : ViewModel() {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    private val _productNotifications = MutableLiveData<List<ProductNotification>>()
    val productNotifications: LiveData<List<ProductNotification>> get() = _productNotifications

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    private val _successMessage = MutableLiveData<String>()
    val successMessage: LiveData<String> get() = _successMessage

    fun loadProductNotificationsForVolunteer(volunteerId: Int) {
        apiService.getProductNotificationsForVolunteer(volunteerId).enqueue(object : Callback<List<ProductNotification>> {
            override fun onResponse(call: Call<List<ProductNotification>>, response: Response<List<ProductNotification>>) {
                if (response.isSuccessful) {
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

    fun updateProductNotification(id: Int, notifiedQuantity: Int, isCollected: Boolean, volunteerId: Int) {
        val updateData = UpdateProductNotificationRequest(notifiedQuantity = notifiedQuantity, isCollected = isCollected)
        apiService.updateProductNotification(id, updateData).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    _successMessage.postValue("Product notification updated successfully!")
                    loadProductNotificationsForVolunteer(volunteerId) // Rafraîchir la liste après mise à jour
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
