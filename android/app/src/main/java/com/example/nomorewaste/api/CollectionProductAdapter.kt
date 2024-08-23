package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class CollectionProductAdapter(
    private val products: List<Product>
) : RecyclerView.Adapter<CollectionProductAdapter.ProductViewHolder>() {

    class ProductViewHolder(view: View) : RecyclerView.ViewHolder(view) {
        val productName: TextView = view.findViewById(R.id.productName)
        val productQuantity: TextView = view.findViewById(R.id.productQuantity)
        val productVolume: TextView = view.findViewById(R.id.productVolume)
    }

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(parent.context)
            .inflate(R.layout.item_product, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        val product = products[position]
        holder.productName.text = product.name ?: "Nom non disponible"
        holder.productQuantity.text = "Quantité : ${product.volume}"
        holder.productVolume.text = "Volume : ${product.volume} m³"
    }

    override fun getItemCount(): Int = products.size
}
