package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class User(
    val id: Int,
    @SerializedName("firstName") val firstName: String,
    @SerializedName("lastName") val lastName: String,
    val email: String,
    @SerializedName("phoneNumber") val phoneNumber: String? = null,
    val password: String? = null
)
