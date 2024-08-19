package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class AvailabilityResponse(
    @SerializedName("dayOfWeek")
    val dayOfWeek: String?,

    @SerializedName("startTime")
    val startTime: String?,

    @SerializedName("endTime")
    val endTime: String?
)
