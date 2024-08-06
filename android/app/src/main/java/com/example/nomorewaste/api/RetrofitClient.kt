package com.example.nomorewaste.api

import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import com.google.gson.GsonBuilder
import com.google.gson.JsonParser
import com.google.gson.stream.JsonReader
import java.util.concurrent.TimeUnit

object RetrofitClient {
        //http://10.0.2.2/
        private const val BASE_URL = "http://10.0.2.2/"

        private val okHttpClient = OkHttpClient.Builder()
                .connectTimeout(30, TimeUnit.SECONDS)
                .readTimeout(30, TimeUnit.SECONDS)
                .build()
        fun getClient(): Retrofit {
                val logging = HttpLoggingInterceptor()
                logging.setLevel(HttpLoggingInterceptor.Level.BODY)

                val httpClient = OkHttpClient.Builder()
                httpClient.addInterceptor(logging)

                val gson = GsonBuilder()
                        .setLenient()
                        .create()

                return Retrofit.Builder()
                        .baseUrl(BASE_URL)
                        .addConverterFactory(GsonConverterFactory.create(gson))
                        .client(httpClient.build())
                        .build()
        }
}
