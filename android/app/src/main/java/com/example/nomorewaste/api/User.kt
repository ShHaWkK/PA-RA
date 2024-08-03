package com.example.nomorewaste.api

data class User(
    val id: Int,
    val first_name: String,
    val last_name: String,
    val email: String,
    val phone_number: String? = null,
    val password: String? = null
)