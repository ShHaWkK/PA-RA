package com.example.nomorewaste.api

data class PlanningResponse(
        val routes: List<Route>?,
        val collections: List<CollectionData>?,
        val deliveries: List<Delivery>?,
        val services: List<ServiceRegistration>?
) {
        fun hasData(): Boolean {
                return (routes?.isNotEmpty() == true) ||
                        (collections?.isNotEmpty() == true) ||
                        (deliveries?.isNotEmpty() == true) ||
                        (services?.isNotEmpty() == true)
        }

        fun getAllSchedules(): List<Any> {
                val combinedList = mutableListOf<Any>()
                routes?.let { combinedList.addAll(it) }
                collections?.let { combinedList.addAll(it) }
                deliveries?.let { combinedList.addAll(it) }
                services?.let { combinedList.addAll(it) }
                return combinedList
        }
}



data class ServiceDetail(
        val name: String,
        val location: String,
        val startSchedule: String,
        val endSchedule: String
)
