package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class RegisterVolunteerRequest(
    @SerializedName("first_name")
    val firstName: String,
    @SerializedName("last_name")
    val lastName: String,
    @SerializedName("email")
    val email: String,
    @SerializedName("phone_number")
    val phoneNumber: String,
    @SerializedName("password")
    val password: String,
    @SerializedName("skills")
    val skills: List<Int>,
    @SerializedName("availabilities")
    val availabilities: MutableList<AvailabilityRequest>
)
