// ApiService.kt
package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path
import retrofit2.http.DELETE

interface ApiService {

    @POST("login")
    fun login(@Body request: LoginRequest): Call<LoginResponse>

    @POST("users/registerVolunteer")
    fun registerVolunteer(@Body request: RegisterVolunteerRequest): Call<Void>

    @POST("users/registerMerchant")
    fun registerMerchant(@Body request: RegisterMerchantRequest): Call<Void>

    @GET("users/{id}")
    fun getUser(@Path("id") id: Int): Call<User>

    @GET("availabilities/{userId}")
    fun getAvailabilities(@Path("userId") userId: Int): Call<List<Availability>>

    @PUT("users/{id}")
    fun updateUser(@Path("id") id: Int, @Body user: User): Call<Void>

    @GET("products")
    fun getProducts(): Call<List<Product>>

    @POST("products")
    fun addProduct(@Body product: Product): Call<Void>

    @PUT("products/{barcode}")
    fun updateProduct(@Path("barcode") barcode: String, @Body product: Product): Call<Void>

    @DELETE("products/{barcode}")
    fun deleteProduct(@Path("barcode") barcode: String): Call<Void>

    @GET("warehouses")
    fun getWarehouses(): Call<List<Warehouse>>

    @GET("recipes")
    fun getRecipes(): Call<List<Recipe>>

    @GET("recipes/{id}")
    fun getRecipeById(@Path("id") id: Int): Call<Recipe>

    @GET("recipes/suggest")
    fun getSuggestedRecipes(): Call<List<Recipe>>
}
