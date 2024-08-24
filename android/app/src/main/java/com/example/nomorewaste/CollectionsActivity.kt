package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.CollectionAdapter
import com.example.nomorewaste.viewmodel.CollectionViewModel
import android.widget.Button

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

        // Initialize the adapter with an empty list initially
        adapter = CollectionAdapter(emptyList()) { collection ->
            // Handle the collection click, e.g., navigate to details
        }
        recyclerView.adapter = adapter

        buttonViewNotifications = findViewById(R.id.button_view_notifications)

        buttonViewNotifications.setOnClickListener {
            // Intent to navigate to ProductNotificationActivity
            val intent = Intent(this, ProductNotificationActivity::class.java)
            startActivity(intent)
        }

        // Observe changes to collections
        collectionViewModel.collections.observe(this, Observer { collections ->
            if (collections != null) {
                // Update adapter with the new data
                adapter.updateData(collections)
            }
        })

        collectionViewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                // Handle the error, e.g., show a Toast
            }
        })

        collectionViewModel.loadAllCollections()
    }
}
