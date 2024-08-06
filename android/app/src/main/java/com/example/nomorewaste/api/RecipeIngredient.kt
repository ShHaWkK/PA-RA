package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class RecipeIngredient(
    val id: Int,
    @SerializedName("recipe_id") val recipeId: Int,
    @SerializedName("product_id") val productId: Int,
    @SerializedName("quantity_needed") val quantityNeeded: Int
)