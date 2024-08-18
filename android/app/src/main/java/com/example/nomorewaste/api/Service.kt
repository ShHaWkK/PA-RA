package com.example.nomorewaste.api

import com.google.gson.annotations.SerializedName

data class Schedule(
    @SerializedName("timezone") val timezone: Timezone,
    @SerializedName("offset") val offset: Int,
    @SerializedName("timestamp") val timestamp: Long
)

data class Timezone(
    @SerializedName("name") val name: String,
    @SerializedName("transitions") val transitions: List<Transition>,
    @SerializedName("location") val location: Location
)

data class Transition(
    @SerializedName("ts") val ts: Long,
    @SerializedName("time") val time: String,
    @SerializedName("offset") val offset: Int,
    @SerializedName("isdst") val isdst: Boolean,
    @SerializedName("abbr") val abbr: String
)

data class Location(
    @SerializedName("country_code") val countryCode: String,
    @SerializedName("latitude") val latitude: Double,
    @SerializedName("longitude") val longitude: Double,
    @SerializedName("comments") val comments: String
)

data class Service(
    @SerializedName("id") val id: Int,
    @SerializedName("name") val name: String,
    @SerializedName("description") val description: String,
    @SerializedName("start_schedule") val startSchedule: String,
    @SerializedName("end_schedule") val endSchedule: String,
    @SerializedName("capacity") val capacity: Int,
    @SerializedName("current_registrations") val currentRegistrations: Int,
    @SerializedName("status") val status: String,
    @SerializedName("location") val location: String
) {
    val remainingCapacity: Int
        get() = capacity - currentRegistrations
}

