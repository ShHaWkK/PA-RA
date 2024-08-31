// ServiceProposal.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceProposal(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String,
    @SerializedName("status") val status: String,
    @SerializedName("created_at") val createdAt: String
)