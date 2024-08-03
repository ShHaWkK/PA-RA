package com.example.nomorewaste.api

data class Availability(
    val id: Int,
    val dayOfWeek: String,
    val startTime: String,
    val endTime: String
)