// Path: src/main/java/com/example/nomorewaste/api/Route.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Route(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("start_time") val startTime: String,
    @SerializedName("end_time") val endTime: String,
    @SerializedName("status") val status: String,
    @SerializedName("driver_id") val driverId: Int
)
