package com.example.nomorewaste.api

data class AddProductRequest(
    val name: String,
    val barcode: String,
    val expiration_date: String,
    val volume: Float,
    val warehouse_id: Int
)
