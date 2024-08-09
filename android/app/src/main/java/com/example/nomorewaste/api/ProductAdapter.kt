package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.CheckBox
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class ProductAdapter(private var productList: List<Product>) : RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    private val selectedProducts = mutableSetOf<Product>()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_products, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        val product = productList[position]
        holder.bind(product)
    }

    override fun getItemCount(): Int = productList.size

    fun updateData(newProducts: List<Product>) {
        productList = newProducts
        notifyDataSetChanged()
    }

    fun getSelectedProducts(): List<Product> = selectedProducts.toList()

    inner class ProductViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productName: TextView = itemView.findViewById(R.id.productName)
        private val checkBox: CheckBox = itemView.findViewById(R.id.checkbox)

        fun bind(product: Product) {
            // Ensure the product has a valid name and barcode before proceeding
            if (!product.name.isNullOrBlank() && !product.barcode.isNullOrBlank()) {
                productName.text = "Produit: ${product.name} - Quantité: ${product.volume} g"
                checkBox.isChecked = selectedProducts.contains(product)

                checkBox.setOnCheckedChangeListener { _, isChecked ->
                    if (isChecked) {
                        selectedProducts.add(product)
                    } else {
                        selectedProducts.remove(product)
                    }
                }
            } else {
                // Handle products with missing name or barcode
                productName.text = "Produit inconnu"
                checkBox.isEnabled = false
            }
        }
    }



}
