package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class UpdateProductNotificationRequest(
    @SerializedName("notified_quantity") val notifiedQuantity: Int,
    @SerializedName("is_collected") val isCollected: Boolean
)
