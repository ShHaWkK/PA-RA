// ProductNotification.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ProductNotification(
    val id: Int,
    val company: Company?,
    val product: Product?,
    @SerializedName("notified_quantity")
    val notifiedQuantity: Int,
    val address: String,
    @SerializedName("is_assigned")
    val isAssigned: Boolean,
    @SerializedName("is_collected")
    val isCollected: Boolean,
    @SerializedName("wished_collection_date")
    val wishedCollectionDate: String?,
    @SerializedName("notified_at")
    val notifiedAt: String?
)

data class Company(
    val id: Int,
    val name: String?,
    val address: String?
)
