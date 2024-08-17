// Path: src/main/java/com/example/nomorewaste/api/ServiceProposalRequest.kt

package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class ServiceProposalRequest(
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String,
    @SerializedName("created_by") val createdBy: Int
)
