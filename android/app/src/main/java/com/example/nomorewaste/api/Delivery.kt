// Path: src/main/java/com/example/nomorewaste/api/Delivery.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Delivery(
    @SerializedName("id") val id: Int,
    @SerializedName("route_name") val routeName: String,
    @SerializedName("delivery_date") val deliveryDate: String,
    @SerializedName("recipient_type") val recipientType: String,
    @SerializedName("status") val status: String
)
