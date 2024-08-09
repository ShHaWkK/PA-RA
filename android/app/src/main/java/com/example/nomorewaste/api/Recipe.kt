package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Recipe(
    @SerializedName("name") val name: String,
    @SerializedName("instructions") val instructions: String,
    @SerializedName("ingredients") val ingredients: List<RecipeIngredient>
)
