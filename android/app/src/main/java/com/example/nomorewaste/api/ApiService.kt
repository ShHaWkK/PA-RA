package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.http.*

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

    @DELETE("products/{id}")
    fun deleteProduct(@Path("id") id: Int): Call<Void>

    @GET("products/id/{id}")
    fun getProduct(@Path("id") id: String): Call<Product>

    @PUT("products/{id}")
    fun updateProduct(@Path("id") id: String, @Body product: Product): Call<Void>

    @GET("stocks/getStocksByWarehouse/{warehouseId}")
    fun getStocksByWarehouse(@Path("warehouseId") warehouseId: Int): Call<List<Stock>>

    @DELETE("products/{barcode}")
    fun deleteProduct(@Path("barcode") barcode: String): Call<Void>

    @GET("products/stock")
    fun getProductsInStock(): Call<Map<String, Product>>

    @GET("services")
    fun getServices(): Call<List<Service>>

    @GET("services/{id}")
    fun getService(@Path("id") id: Int): Call<Service>

    @GET("services/{id}")
    fun getServiceById(@Path("id") serviceId: Int): Call<Service>

    @GET("service_registrations/{userId}")
    fun getUserRegistrations(@Path("userId") userId: Int): Call<List<ServiceRegistration>>

    @DELETE("service_registrations/{id}")
    fun unsubscribeFromService(@Path("id") registrationId: Int): Call<Void>

    @POST("service_registrations")
    fun registerForService(@Body request: ServiceRegistrationRequest): Call<Void>

    @POST("service_proposals")
    fun proposeService(@Body request: ServiceProposalRequest): Call<Void>

    @GET("service_proposals/{user_id}")
    fun getUserProposals(@Path("user_id") userId: Int): Call<List<ServiceProposal>>

    @GET("service_schedules/{user_id}")
    fun getUserSchedule(@Path("user_id") userId: Int): Call<List<ServiceSchedule>>

}