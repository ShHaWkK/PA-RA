package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Availability(
    @SerializedName("day_of_week") val dayOfWeek: String?,
    @SerializedName("start_time") val startTime: String?,
    @SerializedName("end_time") val endTime: String?
)
