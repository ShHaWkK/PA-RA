package com.example.nomorewaste

import android.content.Context
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
    private var volunteerId: Int = -1

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_notifications)

        // Récupérer l'ID du bénévole
        volunteerId = getSharedPreferences("NoMoreWastePrefs", Context.MODE_PRIVATE).getInt("USER_ID", -1)

        recyclerView = findViewById(R.id.recycler_view_notifications)
        recyclerView.layoutManager = LinearLayoutManager(this)

        adapter = ProductNotificationAdapter(emptyList()) { notification, updatedQuantity, isCollected ->
            viewModel.updateProductNotification(notification.id, updatedQuantity, isCollected, volunteerId)
            Toast.makeText(this, "Détails mis à jour", Toast.LENGTH_SHORT).show()
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

        if (volunteerId != -1) {
            viewModel.loadProductNotificationsForVolunteer(volunteerId)
        } else {
            Toast.makeText(this, "Erreur: ID du bénévole manquant", Toast.LENGTH_SHORT).show()
        }
    }
}
