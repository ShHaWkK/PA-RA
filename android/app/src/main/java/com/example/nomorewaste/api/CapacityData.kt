package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class CapacityData(
    @SerializedName("total_capacity") val totalCapacity: Float,
    @SerializedName("occupied_capacity") val occupiedCapacity: Float
)
