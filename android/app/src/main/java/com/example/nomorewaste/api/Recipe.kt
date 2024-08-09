package com.example.nomorewaste.api

import android.os.Parcel
import android.os.Parcelable
import com.example.nomorewaste.api.RecipeIngredient
import com.google.gson.annotations.SerializedName

data class Recipe(
    @SerializedName("name") val name: String,
    @SerializedName("instructions") val instructions: String,
    @SerializedName("ingredients") val ingredients: List<RecipeIngredient>,
    @SerializedName("completion_rate") val completionRate: Double,
    @SerializedName("missing_ingredients") val missingIngredients: List<RecipeIngredient>,
    @SerializedName("tags") val tags: List<String> = listOf()
) : Parcelable {
    constructor(parcel: Parcel) : this(
        parcel.readString() ?: "",
        parcel.readString() ?: "",
        parcel.createTypedArrayList(RecipeIngredient) ?: listOf(),
        parcel.readDouble(),
        parcel.createTypedArrayList(RecipeIngredient) ?: listOf(),
        parcel.createStringArrayList() ?: listOf()
    )

    override fun writeToParcel(parcel: Parcel, flags: Int) {
        parcel.writeString(name)
        parcel.writeString(instructions)
        parcel.writeTypedList(ingredients)
        parcel.writeDouble(completionRate)
        parcel.writeTypedList(missingIngredients)
        parcel.writeStringList(tags)
    }

    override fun describeContents(): Int = 0

    companion object CREATOR : Parcelable.Creator<Recipe> {
        override fun createFromParcel(parcel: Parcel): Recipe = Recipe(parcel)
        override fun newArray(size: Int): Array<Recipe?> = arrayOfNulls(size)
    }
}