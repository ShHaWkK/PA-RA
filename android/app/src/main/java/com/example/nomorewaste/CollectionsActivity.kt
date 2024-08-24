package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.viewmodel.CollectionViewModel
import com.example.nomorewaste.api.CollectionAdapter
import com.google.android.material.floatingactionbutton.FloatingActionButton
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

        buttonViewNotifications = findViewById(R.id.button_view_notifications)

        buttonViewNotifications.setOnClickListener {
            // Intent to navigate to ProductNotificationActivity
            val intent = Intent(this, ProductNotificationActivity::class.java)
            startActivity(intent)
        }

        collectionViewModel.collections.observe(this, Observer { collections ->
            if (collections != null) {
                adapter = CollectionAdapter(collections) { collection ->
                    // Handle the collection click, e.g., navigate to details
                }
                recyclerView.adapter = adapter
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
