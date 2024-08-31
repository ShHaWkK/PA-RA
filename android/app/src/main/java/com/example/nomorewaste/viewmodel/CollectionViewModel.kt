package com.example.nomorewaste.viewmodel

import androidx.lifecycle.LiveData
import androidx.lifecycle.MutableLiveData
import androidx.lifecycle.ViewModel
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.CollectionData  // Assurez-vous d'importer le bon type
import com.example.nomorewaste.api.CollectionDetails
import com.example.nomorewaste.api.RetrofitClient
import okhttp3.ResponseBody
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class CollectionViewModel : ViewModel() {

    private val apiService: ApiService = RetrofitClient.getClient().create(ApiService::class.java)

    private val _collections = MutableLiveData<List<CollectionData>>()  // Utilisez le type correct CollectionData
    val collections: LiveData<List<CollectionData>> get() = _collections

    private val _collectionDetails = MutableLiveData<CollectionDetails>()
    val collectionDetails: LiveData<CollectionDetails> get() = _collectionDetails

    private val _error = MutableLiveData<String>()
    val error: LiveData<String> get() = _error

    private val _exportResponse = MutableLiveData<ResponseBody>()
    val exportResponse: LiveData<ResponseBody> get() = _exportResponse

    private val _sendEmailSuccess = MutableLiveData<Boolean>()
    val sendEmailSuccess: LiveData<Boolean> get() = _sendEmailSuccess

    fun loadAllCollections() {
        apiService.getAllCollections().enqueue(object : Callback<List<CollectionData>> {  // Utilisez le type correct CollectionData
            override fun onResponse(call: Call<List<CollectionData>>, response: Response<List<CollectionData>>) {
                if (response.isSuccessful) {
                    _collections.postValue(response.body())
                } else {
                    _error.postValue("Error: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<List<CollectionData>>, t: Throwable) {  // Utilisez le type correct CollectionData
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

    fun exportCollectionToExcel(collectionId: Int) {
        apiService.exportCollectionToExcel(collectionId).enqueue(object : Callback<ResponseBody> {
            override fun onResponse(call: Call<ResponseBody>, response: Response<ResponseBody>) {
                if (response.isSuccessful) {
                    _exportResponse.postValue(response.body())
                } else {
                    _error.postValue("Error exporting collection: ${response.message()}")
                }
            }

            override fun onFailure(call: Call<ResponseBody>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }

    fun sendCollectionExcelEmail(collectionId: Int, email: String) {
        apiService.sendCollectionExcelEmail(collectionId, email).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    _sendEmailSuccess.postValue(true)
                } else {
                    _error.postValue("Error sending Excel email: ${response.message()} - ${response.errorBody()?.string()}")
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                _error.postValue("Failure: ${t.message}")
            }
        })
    }
}
