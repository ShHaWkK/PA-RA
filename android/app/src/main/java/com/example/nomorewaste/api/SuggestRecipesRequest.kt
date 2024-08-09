package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class SuggestRecipesRequest(
    @SerializedName("products_in_stock") val productsInStock: Map<String, Int>
)
