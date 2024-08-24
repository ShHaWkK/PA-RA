// ProductNotification.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ProductNotification(
    val id: Int,
    @SerializedName("company_id")
    val companyId: Int,
    @SerializedName("company_name")
    val companyName: String?,
    @SerializedName("product_id")
    val productId: Int,
    @SerializedName("product_name")
    val productName: String?,
    @SerializedName("notified_quantity")
    val notifiedQuantity: Int,
    val address: String,
    @SerializedName("is_assigned")
    val isAssigned: Boolean,
    @SerializedName("is_collected")
    val isCollected: Boolean,
    @SerializedName("wished_collection_date")
    val wishedCollectionDate: String,
    @SerializedName("notified_at")
    val notifiedAt: String
)
