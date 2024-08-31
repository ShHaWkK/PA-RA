package com.example.nomorewaste

import android.content.Context
import android.os.Bundle
import android.util.Log
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

        if (volunteerId == -1) {
            Toast.makeText(this, "Erreur: ID du bénévole manquant", Toast.LENGTH_SHORT).show()
            Log.e("ProductNotificationActivity", "ID du bénévole manquant")
            finish() // Fermer l'activité si l'ID du bénévole est manquant
            return
        }

        recyclerView = findViewById(R.id.recycler_view_notifications)
        recyclerView.layoutManager = LinearLayoutManager(this)

        adapter = ProductNotificationAdapter(emptyList()) { notification, updatedQuantity, isCollected ->
            Log.d("ProductNotificationActivity", "Updating notification ID: ${notification.id} with Quantity: $updatedQuantity, isCollected: $isCollected")
            viewModel.updateProductNotification(notification.id, updatedQuantity, isCollected, volunteerId)
        }
        recyclerView.adapter = adapter

        // Observer pour la liste des notifications de produit
        viewModel.productNotifications.observe(this, Observer { notifications ->
            if (notifications != null) {
                Log.d("ProductNotificationActivity", "Loaded ${notifications.size} product notifications.")
                adapter.updateData(notifications)
            }
        })

        // Observer pour les messages d'erreur
        viewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Log.e("ProductNotificationActivity", "Error: $errorMessage")
                Toast.makeText(this, errorMessage, Toast.LENGTH_SHORT).show()
            }
        })

        // Observer pour les messages de succès
        viewModel.successMessage.observe(this, Observer { successMessage ->
            if (successMessage != null) {
                Log.d("ProductNotificationActivity", "Success: $successMessage")
                Toast.makeText(this, successMessage, Toast.LENGTH_SHORT).show()
            }
        })

        // Charger les notifications de produit pour le bénévole
        viewModel.loadProductNotificationsForVolunteer(volunteerId)
    }
}
