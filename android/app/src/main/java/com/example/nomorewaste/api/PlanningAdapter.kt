// Path: src/main/java/com/example/nomorewaste/adapter/PlanningAdapter.kt
package com.example.nomorewaste.adapter

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R
import com.example.nomorewaste.api.*

class PlanningAdapter(
    private val planningItems: List<Any>
) : RecyclerView.Adapter<RecyclerView.ViewHolder>() {

    companion object {
        private const val TYPE_ROUTE = 0
        private const val TYPE_COLLECTION = 1
        private const val TYPE_DELIVERY = 2
        private const val TYPE_SERVICE = 3
    }

    override fun getItemViewType(position: Int): Int {
        return when (planningItems[position]) {
            is Route -> TYPE_ROUTE
            is CollectionData -> TYPE_COLLECTION
            is Delivery -> TYPE_DELIVERY
            is ServiceRegistration -> TYPE_SERVICE
            else -> throw IllegalArgumentException("Invalid type of data")
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecyclerView.ViewHolder {
        return when (viewType) {
            TYPE_ROUTE -> {
                val view = LayoutInflater.from(parent.context).inflate(R.layout.item_route, parent, false)
                RouteViewHolder(view)
            }
            TYPE_COLLECTION -> {
                val view = LayoutInflater.from(parent.context).inflate(R.layout.item_collection, parent, false)
                CollectionViewHolder(view)
            }
            TYPE_DELIVERY -> {
                val view = LayoutInflater.from(parent.context).inflate(R.layout.item_delivery, parent, false)
                DeliveryViewHolder(view)
            }
            TYPE_SERVICE -> {
                val view = LayoutInflater.from(parent.context).inflate(R.layout.item_service, parent, false)
                ServiceViewHolder(view)
            }
            else -> throw IllegalArgumentException("Invalid view type")
        }
    }

    override fun onBindViewHolder(holder: RecyclerView.ViewHolder, position: Int) {
        when (holder) {
            is RouteViewHolder -> holder.bind(planningItems[position] as Route)
            is CollectionViewHolder -> holder.bind(planningItems[position] as CollectionData)
            is DeliveryViewHolder -> holder.bind(planningItems[position] as Delivery)
            is ServiceViewHolder -> holder.bind(planningItems[position] as ServiceRegistration)
        }
    }

    override fun getItemCount(): Int = planningItems.size

    class RouteViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val name: TextView = itemView.findViewById(R.id.route_name)
        private val startTime: TextView = itemView.findViewById(R.id.route_start_time)
        private val endTime: TextView = itemView.findViewById(R.id.route_end_time)
        private val status: TextView = itemView.findViewById(R.id.route_status)

        fun bind(route: Route) {
            name.text = route.name
            startTime.text = route.startTime
            endTime.text = route.endTime
            status.text = route.status
        }
    }

    class CollectionViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val date: TextView = itemView.findViewById(R.id.collection_date)
        private val isCompleted: TextView = itemView.findViewById(R.id.collection_completed)

        fun bind(collection: CollectionData) {
            date.text = collection.collectionDate ?: "Unknown Date"
            isCompleted.text = if (collection.isCompleted) "Completed" else "Pending"
        }
    }

    class DeliveryViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val routeName: TextView = itemView.findViewById(R.id.delivery_route_name)
        private val deliveryDate: TextView = itemView.findViewById(R.id.delivery_date)
        private val recipientType: TextView = itemView.findViewById(R.id.delivery_recipient_type)
        private val status: TextView = itemView.findViewById(R.id.delivery_status)

        fun bind(delivery: Delivery) {
            routeName.text = delivery.routeName
            deliveryDate.text = delivery.deliveryDate
            recipientType.text = delivery.recipientType
            status.text = delivery.status
        }
    }

    class ServiceViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val serviceName: TextView = itemView.findViewById(R.id.service_name)
        private val location: TextView = itemView.findViewById(R.id.service_location)
        private val startSchedule: TextView = itemView.findViewById(R.id.service_start_schedule)
        private val endSchedule: TextView = itemView.findViewById(R.id.service_end_schedule)

        fun bind(serviceRegistration: ServiceRegistration) {
            serviceName.text = serviceRegistration.service?.name
            location.text = serviceRegistration.service?.location
            startSchedule.text = serviceRegistration.service?.startSchedule
            endSchedule.text = serviceRegistration.service?.endSchedule
        }
    }
}
