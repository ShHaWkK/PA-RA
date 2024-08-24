package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceRegistration(
    @SerializedName("id") val id: Int,
    @SerializedName("userId") val userId: Int,
    @SerializedName("registrationDate") val registrationDate: String,
    @SerializedName("createdAt") val createdAt: String,
    @SerializedName("updatedAt") val updatedAt: String,
    val service: Service?
) {
    // Ajoutez ceci pour extraire serviceId de l'objet service
    val serviceId: Int
        get() = service?.id ?: 0
}
