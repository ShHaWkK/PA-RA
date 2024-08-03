package com.example.nomorewaste.api

data class RegisterVolunteerRequest(
    val firstName: String,
    val lastName: String,
    val email: String,
    val phoneNumber: String,
    val password: String,
    val skills: List<String>
)
