// Path: src/main/java/com/example/nomorewaste/api/ServiceRegistration.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceRegistration(
    @SerializedName("id") val id: Int,
    @SerializedName("service_name") val serviceName: String,
    @SerializedName("start_schedule") val startSchedule: String,
    @SerializedName("end_schedule") val endSchedule: String,
    @SerializedName("location") val location: String,
    val service: Service?
)
{
    // Ajoutez ceci pour extraire serviceId de l'objet service
    val serviceId: Int
        get() = service?.id ?: 0
}
