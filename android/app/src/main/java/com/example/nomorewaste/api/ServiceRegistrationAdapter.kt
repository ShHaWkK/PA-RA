package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class ServiceRegistrationAdapter(
    private val services: List<Service>,
    private val onServiceClick: (Service) -> Unit
) : RecyclerView.Adapter<ServiceRegistrationAdapter.ServiceViewHolder>() {

    class ServiceViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val serviceName: TextView = view.findViewById(R.id.service_name)
        val serviceDescription: TextView = view.findViewById(R.id.service_description)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ServiceViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_service, parent, false)
        return ServiceViewHolder(view)
    }

    override fun onBindViewHolder(holder: ServiceViewHolder, position: Int) {
        val service = services[position]
        holder.serviceName.text = service.name
        holder.serviceDescription.text = service.description

        holder.itemView.setOnClickListener {
            onServiceClick(service)
        }
    }

    override fun getItemCount(): Int = services.size
}
