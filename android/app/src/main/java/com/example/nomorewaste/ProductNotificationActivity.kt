// ProductNotificationActivity.kt
package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ProductNotification
import com.example.nomorewaste.api.ProductNotificationAdapter
import com.example.nomorewaste.viewmodel.ProductNotificationViewModel
import com.google.android.material.floatingactionbutton.FloatingActionButton

class ProductNotificationActivity : AppCompatActivity() {

    private val viewModel: ProductNotificationViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: ProductNotificationAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_notifications)

        recyclerView = findViewById(R.id.recycler_view_notifications)
        recyclerView.layoutManager = LinearLayoutManager(this)

        viewModel.productNotifications.observe(this, Observer { notifications ->
            adapter = ProductNotificationAdapter(notifications) { notification, updatedQuantity, isCollected ->
                val updateData = mapOf(
                    "notified_quantity" to updatedQuantity,
                    "is_collected" to isCollected
                )
                viewModel.updateProductNotification(notification.id, updateData)
            }
            recyclerView.adapter = adapter
        })

        viewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
            }
        })

        viewModel.loadAllProductNotifications()
    }
}
