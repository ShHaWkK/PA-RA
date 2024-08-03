package com.example.nomorewaste.api;


data class LoginResponse(
        val token: String,
        val role: String,
        val userId: Int
)