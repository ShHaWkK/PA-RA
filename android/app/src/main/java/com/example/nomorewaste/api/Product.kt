package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Product(
    @SerializedName("name") val name: String,
    @SerializedName("barcode") val barcode: String,
    @SerializedName("expiration_date") val expirationDate: String,
    @SerializedName("volume") val volume: Float,
    @SerializedName("warehouse_id") val warehouseId: Int,
    @SerializedName("scanned") val scanned: Boolean = false
)