package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.Path
import retrofit2.http.PUT
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

    @GET("availabilities/{id}")
    fun getAvailabilities(@Path("id") userId: Int): Call<Any>

    @PUT("users/{id}")
    fun updateUser(@Path("id") id: Int, @Body user: User): Call<Void>

    @GET("warehouses")
    fun getWarehouses(): Call<List<Warehouse>>

    @GET("warehouses/{id}/capacity")
    fun getWarehouseCapacity(@Path("id") warehouseId: Int): Call<CapacityData>

    @GET("recipes")
    fun getRecipes(): Call<List<Recipe>>

    @POST("recipe/suggest")
    fun suggestRecipes(@Body request: SuggestRecipesRequest): Call<List<Recipe>>

    @POST("products")
    fun addProduct(@Body product: Product): Call<Void>

    @GET("products")
    fun getProducts(): Call<List<Product>>

    @PUT("products/{barcode}")
    fun updateProduct(@Path("barcode") barcode: String, @Body product: Product): Call<Void>

    @GET("products/{barcode}")
    fun getProduct(@Path("barcode") barcode: String): Call<Product>

    @GET("stocks/getStocksByWarehouse/{warehouseId}")
    fun getStocksByWarehouse(@Path("warehouseId") warehouseId: Int): Call<List<Stock>>

    @DELETE("products/{barcode}")
    fun deleteProduct(@Path("barcode") barcode: String): Call<Void>

    @GET("products/stock")
    fun getProductsInStock(): Call<Map<String, Product>>
}
