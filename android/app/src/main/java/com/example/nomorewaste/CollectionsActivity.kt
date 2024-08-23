package com.example.nomorewaste

import android.os.Bundle
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.viewmodel.CollectionViewModel
import com.example.nomorewaste.api.CollectionAdapter

class CollectionsActivity : AppCompatActivity() {

    private val collectionViewModel: CollectionViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: CollectionAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_collections)

        recyclerView = findViewById(R.id.recycler_view_collections)
        recyclerView.layoutManager = LinearLayoutManager(this)

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
