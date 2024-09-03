// src/main/java/com/example/nomorewaste/api/Product.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

// Data class for Product entity
data class Product(
    @SerializedName("name") val name: String?,
    @SerializedName("barcode") val barcode: String?,
    @SerializedName("qr_code_path") val qrCodePath: String?,  // Path to the QR code
    @SerializedName("expiration_date") val expirationDate: String?,
    @SerializedName("volume") val volume: Float,
    @SerializedName("warehouse_id") val warehouseId: Int,
    @SerializedName("scanned") val scanned: Boolean = false
) {
    override fun equals(other: Any?): Boolean {
        if (this === other) return true
        if (other !is Product) return false

        if (name != other.name) return false
        if (barcode != other.barcode) return false

        return true
    }

    override fun hashCode(): Int {
        var result = name?.hashCode() ?: 0
        result = 31 * result + (barcode?.hashCode() ?: 0)
        return result
    }
}

// Data class for ProductNotification entity
data class ProductNotification(
    val id: Int,
    val company: Company?,
    val product: Product?,
    @SerializedName("notified_quantity") val notifiedQuantity: Int,
    val address: String,
    @SerializedName("is_assigned") val isAssigned: Boolean,
    @SerializedName("is_collected") val isCollected: Boolean,
    @SerializedName("wished_collection_date") val wishedCollectionDate: String?,
    @SerializedName("notified_at") val notifiedAt: String?
)

// Data class for Company entity
data class Company(
    val id: Int,
    val name: String?,
    val address: String?
)
