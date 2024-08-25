package com.example.nomorewaste

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.Button
import android.widget.CheckBox
import android.widget.NumberPicker
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ProductNotification

class ProductNotificationAdapter(
    private var productNotifications: List<ProductNotification>,
    private val onUpdateClick: (ProductNotification, Int, Boolean) -> Unit
) : RecyclerView.Adapter<ProductNotificationAdapter.NotificationViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): NotificationViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_notification, parent, false)
        return NotificationViewHolder(view)
    }

    override fun onBindViewHolder(holder: NotificationViewHolder, position: Int) {
        val notification = productNotifications[position]
        holder.bind(notification, onUpdateClick)
    }

    override fun getItemCount(): Int = productNotifications.size

    fun updateData(newNotifications: List<ProductNotification>) {
        productNotifications = newNotifications
        notifyDataSetChanged()
    }

    class NotificationViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productName: TextView = itemView.findViewById(R.id.text_view_product_name)
        private val numberPicker: NumberPicker = itemView.findViewById(R.id.number_picker_quantity)
        private val checkBoxCollected: CheckBox = itemView.findViewById(R.id.checkbox_collected)
        private val updateButton: Button = itemView.findViewById(R.id.button_update)

        fun bind(notification: ProductNotification, onUpdateClick: (ProductNotification, Int, Boolean) -> Unit) {
            productName.text = notification.product?.name ?: "Unknown Product"

            numberPicker.minValue = 0
            numberPicker.maxValue = 100
            numberPicker.value = notification.notifiedQuantity

            checkBoxCollected.isChecked = notification.isCollected

            updateButton.setOnClickListener {
                onUpdateClick(notification, numberPicker.value, checkBoxCollected.isChecked)
            }
        }
    }
}
