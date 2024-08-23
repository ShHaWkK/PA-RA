package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Collection
import com.example.nomorewaste.api.CollectionDetails
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class CollectionViewModel : ViewModel() {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    private val _collections = MutableLiveData<List<Collection>>()
    val collections: LiveData<List<Collection>> get() = _collections

    private val _collectionDetails = MutableLiveData<CollectionDetails>()
    val collectionDetails: LiveData<CollectionDetails> get() = _collectionDetails

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    fun loadAllCollections() {
        apiService.getAllCollections().enqueue(object : Callback<List<Collection>> {
            override fun onResponse(call: Call<List<Collection>>, response: Response<List<Collection>>) {
                if (response.isSuccessful) {
                    val collections = response.body()?.map { collection ->
                        collection.copy(
                            volunteerName = collection.volunteerName ?: "Unknown Volunteer",
                            vehicleLicensePlate = collection.vehicleLicensePlate ?: "Unknown Vehicle",
                            collectionDate = collection.collectionDate ?: "Unknown Date"
                        )
                    } ?: emptyList()
                    _collections.postValue(collections)
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<List<Collection>>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }

    fun loadCollectionDetails(collectionId: Int) {
        apiService.getCollectionDetails(collectionId).enqueue(object : Callback<CollectionDetails> {
            override fun onResponse(call: Call<CollectionDetails>, response: Response<CollectionDetails>) {
                if (response.isSuccessful) {
                    _collectionDetails.postValue(response.body())
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<CollectionDetails>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }

    fun createCollectionRequest(requestData: Map<String, Any>) {
        apiService.createCollectionRequest(requestData).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    _error.postValue(null)
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }


    // Other functions omitted for brevity
}
