package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class User(
    val id: Int,
    @SerializedName("first_name") val firstName: String,
    @SerializedName("last_name") val lastName: String,
    val email: String,
    @SerializedName("phone_number") val phoneNumber: String? = null,
    val password: String? = null
)
