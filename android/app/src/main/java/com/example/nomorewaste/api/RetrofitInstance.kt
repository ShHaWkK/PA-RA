// Path: src/main/java/com/example/nomorewaste/api/RetrofitInstance.kt
package com.example.nomorewaste.api

object RetrofitInstance {

    val api: ApiService by lazy {
        RetrofitClient.getClient().create(ApiService::class.java)
    }
}
