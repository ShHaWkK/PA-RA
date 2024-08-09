package com.example.nomorewaste.api

import android.os.Parcel
import android.os.Parcelable
import com.google.gson.annotations.SerializedName

data class RecipeIngredient(
    @SerializedName("product_name") val productName: String,
    @SerializedName("quantity_needed") val quantityNeeded: Int,
    @SerializedName("is_vegetarian") val isVegetarian: Boolean = false,
    @SerializedName("contains_gluten") val containsGluten: Boolean = false
) : Parcelable {
    constructor(parcel: Parcel) : this(
        parcel.readString() ?: "",
        parcel.readInt(),
        parcel.readByte() != 0.toByte(),
        parcel.readByte() != 0.toByte()
    )

    override fun writeToParcel(parcel: Parcel, flags: Int) {
        parcel.writeString(productName)
        parcel.writeInt(quantityNeeded)
        parcel.writeByte(if (isVegetarian) 1 else 0)
        parcel.writeByte(if (containsGluten) 1 else 0)
    }

    override fun describeContents(): Int = 0

    companion object CREATOR : Parcelable.Creator<RecipeIngredient> {
        override fun createFromParcel(parcel: Parcel): RecipeIngredient = RecipeIngredient(parcel)
        override fun newArray(size: Int): Array<RecipeIngredient?> = arrayOfNulls(size)
    }
}