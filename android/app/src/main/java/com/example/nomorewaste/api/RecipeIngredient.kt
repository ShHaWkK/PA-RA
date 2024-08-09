package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class RecipeIngredient(
    @SerializedName("product_name") val productName: String,
    @SerializedName("quantity_needed") val quantityNeeded: Int
)
