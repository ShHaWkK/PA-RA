package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceSchedule(
    @SerializedName("id") val id: Int,
    @SerializedName("service") val service: Service,
    @SerializedName("startTime") val startTime: String,
    @SerializedName("endTime") val endTime: String,
    @SerializedName("location") val location: String
)