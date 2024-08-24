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
    private var notifications: List<ProductNotification>,
    private val onUpdateClick: (ProductNotification, Int, Boolean) -> Unit
) : RecyclerView.Adapter<ProductNotificationAdapter.NotificationViewHolder>() {

    fun updateData(newNotifications: List<ProductNotification>) {
        notifications = newNotifications
        notifyDataSetChanged()
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): NotificationViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_notification, parent, false)
        return NotificationViewHolder(view)
    }

    override fun onBindViewHolder(holder: NotificationViewHolder, position: Int) {
        val notification = notifications[position]
        holder.bind(notification)
    }

    override fun getItemCount(): Int = notifications.size

    inner class NotificationViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productNameTextView: TextView = itemView.findViewById(R.id.text_view_product_name)
        private val notifiedQuantityEditText: EditText = itemView.findViewById(R.id.edit_text_notified_quantity)
        private val collectedCheckBox: CheckBox = itemView.findViewById(R.id.checkbox_collected)
        private val updateButton: Button = itemView.findViewById(R.id.button_update)

        fun bind(notification: ProductNotification) {
            productNameTextView.text = notification.product?.name?.takeIf { it.isNotBlank() } ?: "Unknown Product"
            notifiedQuantityEditText.setText(notification.notifiedQuantity.toString())
            collectedCheckBox.isChecked = notification.isCollected

            updateButton.setOnClickListener {
                val updatedQuantity = notifiedQuantityEditText.text.toString().toIntOrNull() ?: 0
                val isCollected = collectedCheckBox.isChecked
                onUpdateClick(notification, updatedQuantity, isCollected)
            }
        }
    }
}

