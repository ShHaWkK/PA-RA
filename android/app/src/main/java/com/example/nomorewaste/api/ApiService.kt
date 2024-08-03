package com.example.nomorewaste.api

import retrofit2.Call
import retrofit2.http.Body
import retrofit2.http.GET
import retrofit2.http.POST
import retrofit2.http.PUT
import retrofit2.http.Path

interface ApiService {

    @POST("login")
    fun login(@Body request: LoginRequest): Call<LoginResponse>

    @POST("registerVolunteer")
    fun registerVolunteer(@Body request: RegisterVolunteerRequest): Call<Void>

    @POST("registerMerchant")
    fun registerMerchant(@Body request: RegisterMerchantRequest): Call<Void>

    @GET("users/{id}")
    fun getUser(@Path("id") id: Int): Call<User>

    @GET("availabilities/{userId}")
    fun getAvailabilities(@Path("userId") userId: Int): Call<List<Availability>>

    @PUT("users/{id}")
    fun updateUser(@Path("id") id: Int, @Body user: User): Call<Void>
}
