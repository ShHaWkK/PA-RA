package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.CheckBox
import android.widget.ImageButton
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class ProductAdapter(
    private var productList: List<Product>,
    private val onEditClick: (Product) -> Unit,
    private val onDeleteClick: (Product) -> Unit
) : RecyclerView.Adapter<ProductAdapter.ProductViewHolder>() {

    private val selectedProducts = mutableSetOf<Product>()

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): ProductViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_product, parent, false)
        return ProductViewHolder(view)
    }

    override fun onBindViewHolder(holder: ProductViewHolder, position: Int) {
        val product = productList[position]
        holder.bind(product)
    }

    override fun getItemCount(): Int = productList.size

    fun getSelectedProducts(): List<Product> {
        return selectedProducts.toList()
    }

    inner class ProductViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productName: TextView = itemView.findViewById(R.id.productName)
        private val productQuantity: TextView = itemView.findViewById(R.id.productQuantity)
        private val productVolume: TextView = itemView.findViewById(R.id.productVolume)
        private val checkBox: CheckBox = itemView.findViewById(R.id.checkbox)

        fun bind(product: Product) {
            productName.text = product.name ?: "Nom non disponible"
            productQuantity.text = "Quantité : ${product.volume}"
            productVolume.text = "Volume : ${product.volume} m³"
            checkBox.isChecked = selectedProducts.contains(product)

            checkBox.setOnCheckedChangeListener { _, isChecked ->
                if (isChecked) {
                    selectedProducts.add(product)
                } else {
                    selectedProducts.remove(product)
                }
            }
        }
    }
}
