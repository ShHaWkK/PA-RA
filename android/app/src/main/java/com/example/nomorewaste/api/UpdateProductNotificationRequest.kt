package com.example.nomorewaste.api

data class UpdateProductNotificationRequest(
    val notified_quantity: Int,
    val is_collected: Boolean
)
