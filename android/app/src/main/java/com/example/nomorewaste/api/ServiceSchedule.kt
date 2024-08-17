package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceSchedule(
    @SerializedName("timestamp")
    val timestamp: Long
)