package com.example.nomorewaste.api

import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory

object RetrofitClient {
        private const val BASE_URL = "http://10.0.2.2/"

        fun getClient(): Retrofit {
                val logging = HttpLoggingInterceptor()
                logging.setLevel(HttpLoggingInterceptor.Level.BODY)

                val httpClient = OkHttpClient.Builder()
                httpClient.addInterceptor(logging)

                return Retrofit.Builder()
                        .baseUrl(BASE_URL)
                        .addConverterFactory(GsonConverterFactory.create())
                        .client(httpClient.build())
                        .build()
        }
}