package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Stock(
    @SerializedName("id") val id: Int,
    @SerializedName("product_id") val productId: Int,
    @SerializedName("product_name") val productName: String?,
    @SerializedName("quantity") val quantity: Int,
    @SerializedName("volume") val volume: Float,
    @SerializedName("entry_date") val entryDate: String,
    @SerializedName("exit_date") val exitDate: String?,
    @SerializedName("availability") val availability: String,
    @SerializedName("warehouse_id") val warehouseId: Int
)
