package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Collection(
    val id: Int,
    @SerializedName("volunteer_id")
    val volunteerId: Int?,
    @SerializedName("volunteer_name")
    val volunteerName: String?,
    @SerializedName("volunteer_email")
    val volunteerEmail: String?,
    @SerializedName("vehicle_id")
    val vehicleId: Int?,
    @SerializedName("vehicle_license_plate")
    val vehicleLicensePlate: String?,
    @SerializedName("collection_date")
    val collectionDate: String?,
    @SerializedName("is_completed")
    val isCompleted: Boolean,
    @SerializedName("created_at")
    val createdAt: String?,
    @SerializedName("updated_at")
    val updatedAt: String?
)