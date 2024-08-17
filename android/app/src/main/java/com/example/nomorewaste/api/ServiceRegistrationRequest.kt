// Path: src/main/java/com/example/nomorewaste/api/ServiceRegistrationRequest.kt

package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceRegistrationRequest(
    @SerializedName("service_id") val serviceId: Int,
    @SerializedName("user_id") val userId: Int
)
