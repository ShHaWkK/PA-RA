package com.example.nomorewaste

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.CollectionData

class CollectionAdapter(
    private var collections: List<CollectionData>,
    private val onExportClick: (CollectionData) -> Unit,
    private val onSendEmailClick: (CollectionData) -> Unit,
    private val onViewNotificationsClick: () -> Unit
) : RecyclerView.Adapter<CollectionAdapter.CollectionViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): CollectionViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_collection, parent, false)
        return CollectionViewHolder(view)
    }

    override fun onBindViewHolder(holder: CollectionViewHolder, position: Int) {
        val collection = collections[position]
        holder.bind(collection)
    }

    override fun getItemCount() = collections.size

    fun updateData(newCollections: List<CollectionData>) {
        collections = newCollections
        notifyDataSetChanged()
    }

    inner class CollectionViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val dateTextView: TextView = itemView.findViewById(R.id.text_view_date)
        private val volunteerTextView: TextView = itemView.findViewById(R.id.text_view_volunteer)
        private val vehicleTextView: TextView = itemView.findViewById(R.id.text_view_vehicle)
        private val completedTextView: TextView = itemView.findViewById(R.id.text_view_completed)
        private val exportButton: Button = itemView.findViewById(R.id.button_export_excel)
        private val sendEmailButton: Button = itemView.findViewById(R.id.button_send_excel_email)

        fun bind(collection: CollectionData) {
            dateTextView.text = collection.collectionDate ?: "Unknown Date"
            volunteerTextView.text = collection.volunteerName ?: "Unknown Volunteer"
            vehicleTextView.text = collection.vehicleLicensePlate ?: "Unknown Vehicle"
            completedTextView.text = if (collection.isCompleted) "Yes" else "No"

            exportButton.setOnClickListener {
                onExportClick(collection)
            }

            sendEmailButton.setOnClickListener {
                onSendEmailClick(collection)
            }

            itemView.setOnClickListener {
                onViewNotificationsClick()
            }
        }
    }
}
