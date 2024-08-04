package com.example.nomorewaste.api

data class RegisterMerchantRequest(
    val first_name: String,
    val last_name: String,
    val email: String,
    val phone_number: String,
    val password: String,
    val company_name: String? = null,
    val siret: String? = null,
    val address: String? = null,
    val contact_info: String? = null
)
