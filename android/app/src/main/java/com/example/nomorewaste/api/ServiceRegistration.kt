package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceRegistration(
    @SerializedName("id") val id: Int,
    @SerializedName("serviceId") val serviceId: Int,
    @SerializedName("userId") val userId: Int,
    @SerializedName("registrationDate") val registrationDate: String,
    @SerializedName("createdAt") val createdAt: String,
    @SerializedName("updatedAt") val updatedAt: String
)
