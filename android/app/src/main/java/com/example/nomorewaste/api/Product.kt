// src/main/java/com/example/nomorewaste/api/Product.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Product(
    @SerializedName("name") val name: String?,
    @SerializedName("barcode") val barcode: String?,
    @SerializedName("qr_code_path") val qrCodePath: String?,  // Path to the QR code
    @SerializedName("expiration_date") val expirationDate: String?,
    @SerializedName("volume") val volume: Float,
    @SerializedName("warehouse_id") val warehouseId: Int,
    @SerializedName("scanned") val scanned: Boolean = false
)

{
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
