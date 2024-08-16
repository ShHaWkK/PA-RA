package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Warehouse(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("address") val address: String,
    @SerializedName("contact_info") val contactInfo: String,
    @SerializedName("capacity") val capacity: Int, // Add this line to include capacity
    @SerializedName("city") val city: String,
    @SerializedName("country") val country: String
)
