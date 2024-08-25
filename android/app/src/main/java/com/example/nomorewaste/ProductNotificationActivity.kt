package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.viewmodel.ProductNotificationViewModel

class ProductNotificationActivity : AppCompatActivity() {

    private val viewModel: ProductNotificationViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: ProductNotificationAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_notifications)

        recyclerView = findViewById(R.id.recycler_view_notifications)
        recyclerView.layoutManager = LinearLayoutManager(this)

        adapter = ProductNotificationAdapter(emptyList()) { notification, updatedQuantity, isCollected ->
            viewModel.updateProductNotification(notification.id, updatedQuantity, isCollected)
            Toast.makeText(this, "Details updated", Toast.LENGTH_SHORT).show()
        }
        recyclerView.adapter = adapter

        viewModel.productNotifications.observe(this, Observer { notifications ->
            if (notifications != null) {
                adapter.updateData(notifications)
            }
        })

        viewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
            }
        })

        viewModel.loadAllProductNotifications()
    }
}
