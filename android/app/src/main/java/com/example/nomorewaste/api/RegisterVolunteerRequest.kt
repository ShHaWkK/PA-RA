package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName


data class RegisterVolunteerRequest(
    val first_name: String,
    val last_name: String,
    val email: String,
    val phone_number: String,
    val password: String,
    val availabilities: List<Availability>
)