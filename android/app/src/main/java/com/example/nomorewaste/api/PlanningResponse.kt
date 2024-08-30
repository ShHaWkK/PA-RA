// Path: src/main/java/com/example/nomorewaste/api/PlanningResponse.kt
package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class PlanningResponse(
        val routes: List<Route> = emptyList(),
        val collections: List<CollectionData> = emptyList(),
        val deliveries: List<Delivery> = emptyList(),
        val services: List<ServiceRegistration> = emptyList()
) {
        fun toList(): List<Any> {
                val list = mutableListOf<Any>()
                list.addAll(routes)
                list.addAll(collections)
                list.addAll(deliveries)
                list.addAll(services)
                return list
        }
}
