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
    private val onUpdateClickListener: (ProductNotification, Int, Boolean) -> Unit
) : RecyclerView.Adapter<ProductNotificationAdapter.ViewHolder>() {

    inner class ViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productNameTextView: TextView = itemView.findViewById(R.id.text_view_product_name)
        private val companyNameTextView: TextView = itemView.findViewById(R.id.text_view_company_name)
        private val addressTextView: TextView = itemView.findViewById(R.id.text_view_address)
        private val quantityNumberPicker: NumberPicker = itemView.findViewById(R.id.number_picker_quantity)
        private val collectedCheckBox: CheckBox = itemView.findViewById(R.id.checkbox_collected)
        private val updateButton: Button = itemView.findViewById(R.id.button_update)

        fun bind(notification: ProductNotification) {
            productNameTextView.text = notification.product?.name ?: "Unknown Product"
            companyNameTextView.text = notification.company?.name ?: "Unknown Company"
            addressTextView.text = notification.address
            quantityNumberPicker.minValue = 0
            quantityNumberPicker.maxValue = 100 // Adjust as needed
            quantityNumberPicker.value = notification.notifiedQuantity

            collectedCheckBox.isChecked = notification.isCollected

            updateButton.setOnClickListener {
                onUpdateClickListener(notification, quantityNumberPicker.value, collectedCheckBox.isChecked)
            }
        }
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_notification, parent, false)
        return ViewHolder(view)
    }

    override fun onBindViewHolder(holder: ViewHolder, position: Int) {
        holder.bind(productNotifications[position])
    }

    override fun getItemCount(): Int = productNotifications.size

    fun updateData(newProductNotifications: List<ProductNotification>) {
        this.productNotifications = newProductNotifications
        notifyDataSetChanged()
    }
}
