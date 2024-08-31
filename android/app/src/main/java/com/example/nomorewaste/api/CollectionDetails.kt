package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class CollectionDetails(
    val id: Int,
    @SerializedName("volunteer_name")
    val volunteerName: String,
    @SerializedName("vehicle_license_plate")
    val vehicleLicensePlate: String,
    @SerializedName("collection_date")
    val collectionDate: String,
    @SerializedName("is_completed")
    val isCompleted: Boolean,
    val products: List<Product>
)
