package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.Collection
import com.example.nomorewaste.viewmodel.CollectionViewModel

class CollectionsActivity : AppCompatActivity() {

    private val collectionViewModel: CollectionViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: CollectionAdapter
    private lateinit var buttonViewNotifications: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_collections)

        recyclerView = findViewById(R.id.recycler_view_collections)
        recyclerView.layoutManager = LinearLayoutManager(this)

        adapter = CollectionAdapter(
            collections = emptyList(),
            onExportClick = { collection ->
                collectionViewModel.exportCollectionToExcel(collection.id)
            },
            onSendEmailClick = { collection ->
                val userEmail = "user@example.com" // This should be dynamically fetched
                collectionViewModel.sendCollectionExcelEmail(collection.id, userEmail)
            },
            onViewNotificationsClick = {
                val intent = Intent(this, ProductNotificationActivity::class.java)
                startActivity(intent)
            }
        )
        recyclerView.adapter = adapter

        // Initialize the button and set click listener
        buttonViewNotifications = findViewById(R.id.button_view_notifications)
        buttonViewNotifications.setOnClickListener {
            Toast.makeText(this, "View Notifications Clicked", Toast.LENGTH_SHORT).show() // Debugging log
            val intent = Intent(this, ProductNotificationActivity::class.java)
            startActivity(intent)
        }

        // Observer logic for ViewModel
        collectionViewModel.collections.observe(this, Observer { collections ->
            if (collections != null) {
                adapter.updateData(collections)
            }
        })

        collectionViewModel.exportResponse.observe(this, Observer { responseBody ->
            if (responseBody != null) {
                Toast.makeText(this, "Collection exported successfully!", Toast.LENGTH_SHORT).show()
            }
        })

        collectionViewModel.sendEmailSuccess.observe(this, Observer { success ->
            if (success) {
                Toast.makeText(this, "Excel sent successfully via email!", Toast.LENGTH_SHORT).show()
            }
        })

        collectionViewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, errorMessage, Toast.LENGTH_LONG).show()  // Make it longer to ensure it's readable
            }
        })

        collectionViewModel.loadAllCollections()
    }
}
