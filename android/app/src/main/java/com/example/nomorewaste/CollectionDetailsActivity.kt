package com.example.nomorewaste

import android.os.Bundle
import android.widget.Toast
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import androidx.lifecycle.Observer
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.viewmodel.CollectionViewModel
import com.example.nomorewaste.api.CollectionProductAdapter

class CollectionDetailsActivity : AppCompatActivity() {

    private val collectionViewModel: CollectionViewModel by viewModels()
    private lateinit var recyclerView: RecyclerView
    private lateinit var adapter: CollectionProductAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_collection_details)

        val collectionId = intent.getIntExtra("collection_id", -1)
        if (collectionId == -1) {
            Toast.makeText(this, "Invalid collection ID", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        recyclerView = findViewById(R.id.recycler_view_collection_details)
        recyclerView.layoutManager = LinearLayoutManager(this)

        collectionViewModel.collectionDetails.observe(this, Observer { details ->
            if (details != null) {
                adapter = CollectionProductAdapter(details.products)
                recyclerView.adapter = adapter
            } else {
                Toast.makeText(this, "No collection details found", Toast.LENGTH_SHORT).show()
            }
        })

        collectionViewModel.error.observe(this, Observer { errorMessage ->
            if (errorMessage != null) {
                Toast.makeText(this, "Error: $errorMessage", Toast.LENGTH_SHORT).show()
            }
        })

        collectionViewModel.loadCollectionDetails(collectionId)
    }
}
