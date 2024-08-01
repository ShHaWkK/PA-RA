package com.example.nomorewaste.api

data class RegisterMerchantRequest(
    val firstName: String,
    val lastName: String,
    val email: String,
    val phoneNumber: String,
    val password: String,
    val role: String,
    val companyName: String? = null,
    val siret: String? = null,
    val address: String? = null
)