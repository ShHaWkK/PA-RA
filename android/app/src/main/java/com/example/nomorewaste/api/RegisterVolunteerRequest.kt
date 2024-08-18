package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class RegisterVolunteerRequest(
    @SerializedName("first_name") val firstName: String,
    @SerializedName("last_name") val lastName: String,
    val email: String,
    @SerializedName("phone_number") val phoneNumber: String,
    val password: String,
    val skills: List<Int>,
    val availabilities: List<Availability>
)
