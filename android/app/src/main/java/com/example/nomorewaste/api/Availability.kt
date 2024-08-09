package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Availability(
    @SerializedName("dayOfWeek") val day_of_week: String?,
    @SerializedName("startTime") val start_time: String?,
    @SerializedName("endTime") val end_time: String?
)
