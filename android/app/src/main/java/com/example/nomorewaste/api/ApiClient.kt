package com.example.nomorewaste.api

import android.util.Log
import com.example.nomorewaste.model.PlanningItem
import okhttp3.OkHttpClient
import okhttp3.Request
import org.json.JSONArray
import org.json.JSONObject
import java.io.IOException

object ApiClient {

    private val client = OkHttpClient()

    suspend fun fetchPlanningByUserIdAndDate(userId: Int, date: String): List<PlanningItem> {
        val url = "http://10.0.2.2/planning?user_id=$userId&date=$date"
        val request = Request.Builder().url(url).build()

        client.newCall(request).execute().use { response ->
            if (!response.isSuccessful) throw Exception("Unexpected code $response")

            val responseBody = response.body?.string() ?: throw Exception("Empty response body")

            val jsonObject = JSONObject(responseBody)

            return parsePlanningItems(jsonObject)
        }
    }

    private fun parsePlanningItems(jsonObject: JSONObject): List<PlanningItem> {
        val planningItems = mutableListOf<PlanningItem>()

        val routesArray = jsonObject.optJSONArray("routes") ?: JSONArray()
        for (i in 0 until routesArray.length()) {
            val routeObject = routesArray.getJSONObject(i)
            val planningItem = PlanningItem(
                id = routeObject.getInt("id"),
                title = routeObject.getString("name"),
                startTime = routeObject.getString("start_time"),
                endTime = routeObject.optString("end_time", "null"),
                dateRange = "${routeObject.getString("start_time")} - ${routeObject.optString("end_time", "null")}"
            )
            planningItems.add(planningItem)
        }

        val collectionsArray = jsonObject.optJSONArray("collections") ?: JSONArray()
        for (i in 0 until collectionsArray.length()) {
            val collectionObject = collectionsArray.getJSONObject(i)
            val planningItem = PlanningItem(
                id = collectionObject.getInt("id"),
                title = "Collection on ${collectionObject.getString("collection_date")}",
                startTime = collectionObject.getString("collection_date"),
                endTime = "",
                dateRange = collectionObject.getString("collection_date")
            )
            planningItems.add(planningItem)
        }

        val servicesArray = jsonObject.optJSONArray("services") ?: JSONArray()
        for (i in 0 until servicesArray.length()) {
            val serviceObject = servicesArray.getJSONObject(i)
            val serviceDetails = serviceObject.getJSONObject("service")
            val planningItem = PlanningItem(
                id = serviceObject.getInt("id"),
                title = serviceDetails.getString("name"),
                startTime = serviceDetails.getString("schedule"),
                endTime = "",
                dateRange = serviceDetails.getString("schedule")
            )
            planningItems.add(planningItem)
        }

        return planningItems
    }
}
