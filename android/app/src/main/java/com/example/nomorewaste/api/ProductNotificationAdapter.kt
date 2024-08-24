package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.CheckBox
import android.widget.EditText
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R
import com.example.nomorewaste.api.ProductNotification

class ProductNotificationAdapter(
    private val productNotifications: List<ProductNotification>,
    private val onUpdate: (ProductNotification, Int, Boolean) -> Unit
) : RecyclerView.Adapter<ProductNotificationAdapter.NotificationViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): NotificationViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_product_notification, parent, false)
        return NotificationViewHolder(view)
    }

    override fun onBindViewHolder(holder: NotificationViewHolder, position: Int) {
        val notification = productNotifications[position]
        holder.bind(notification)
    }

    override fun getItemCount() = productNotifications.size

    inner class NotificationViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productNameTextView: TextView = itemView.findViewById(R.id.text_view_product_name)
        private val notifiedQuantityEditText: EditText = itemView.findViewById(R.id.edit_text_notified_quantity)
        private val collectedCheckbox: CheckBox = itemView.findViewById(R.id.checkbox_collected)
        private val updateButton: Button = itemView.findViewById(R.id.button_update)

        fun bind(notification: ProductNotification) {
            productNameTextView.text = notification.productName ?: "Unknown Product"
            notifiedQuantityEditText.setText(notification.notifiedQuantity.toString())
            collectedCheckbox.isChecked = notification.isCollected

            updateButton.setOnClickListener {
                val updatedQuantity = notifiedQuantityEditText.text.toString().toIntOrNull() ?: notification.notifiedQuantity
                val isCollected = collectedCheckbox.isChecked
                onUpdate(notification, updatedQuantity, isCollected)
            }
        }
    }
}