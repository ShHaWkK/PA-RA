package com.example.nomorewaste.api

import okhttp3.ResponseBody
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
 fun getAvailabilities(@Path("id") userId: Int): Call<ResponseBody>
    @PUT("users/{id}")
    fun updateUser(@Path("id") id: Int, @Body user: User): Call<Void>

    @GET("warehouses")
    fun getWarehouses(): Call<List<Warehouse>>

    @GET("warehouses/{id}/capacity")
    fun getWarehouseCapacity(@Path("id") warehouseId: Int): Call<CapacityData>

    @GET("recipe")
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

    @GET("skills")
    fun getSkills(): Call<List<Skill>>

    @POST("service_registrations")
    fun registerForService(@Body request: ServiceRegistrationRequest): Call<Void>

    @POST("service_proposals")
    fun proposeService(@Body request: ServiceProposalRequest): Call<Void>

    @GET("service_proposals/{user_id}")
    fun getUserProposals(@Path("user_id") userId: Int): Call<List<ServiceProposal>>

    @GET("service_schedules/{user_id}")
    fun getUserSchedule(@Path("user_id") userId: Int): Call<List<ServiceSchedule>>

    @GET("collections")
    fun getAllCollections(): Call<List<Collection>>

    @GET("collections/{id}")
    fun getCollectionDetails(@Path("id") collectionId: Int): Call<CollectionDetails>

    @POST("collections")
    fun createCollection(@Body collectionData: Map<String, Any>): Call<Collection>

    @PUT("collections/{id}")
    fun updateCollection(@Path("id") collectionId: Int, @Body collectionData: Map<String, Any>): Call<Collection>

    @GET("collections/{id}/export")
    fun exportCollectionToExcel(@Path("id") collectionId: Int): Call<ResponseBody>

    // Update this POST request to include correct path and query parameters
    @POST("collections/{id}/send_excel")
    fun sendCollectionExcelEmail(
        @Path("id") collectionId: Int,
        @Query("email") email: String
    ): Call<Void>

    @POST("/collection_requests")
    fun createCollectionRequest(@Body requestData: Map<String, Any>): Call<Void>

    @GET("product_notifications")
    fun getAllProductNotifications(): Call<List<ProductNotification>>

    @GET("product_notifications/{id}")
    fun getProductNotification(@Path("id") id: Int): Call<ProductNotification>

    @PUT("product_notifications/{id}")
    fun updateProductNotification(
        @Path("id") id: Int,
        @Body updateData: UpdateProductNotificationRequest
    ): Call<Void>
    @POST("product_notifications")
    fun createProductNotification(@Body requestData: Map<String, Any>): Call<ProductNotification>

    @DELETE("product_notifications/{id}")
    fun deleteProductNotification(@Path("id") id: Int): Call<Void>
}

