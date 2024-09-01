// Path: src/main/java/com/example/nomorewaste/model/PlanningItem.kt
package com.example.nomorewaste.model

data class PlanningItem(
    val id: Int,
    val title: String,
    val startTime: String,
    val endTime: String,
    val dateRange: String
)
