package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Availability(
    @SerializedName("dayOfWeek")
    val dayOfWeek: String,
    @SerializedName("startTime")
    val startTime: String,
    @SerializedName("endTime")
    val endTime: String
)
