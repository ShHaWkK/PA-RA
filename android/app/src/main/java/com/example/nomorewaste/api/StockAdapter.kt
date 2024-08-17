package com.example.nomorewaste.api

import android.content.Context
import android.content.Intent
import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.ImageButton
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.ProductDetailActivity
import com.example.nomorewaste.R

class StockAdapter(
    private var stocks: List<Stock>,
    private val context: Context,
    private val onDeleteClick: (Stock) -> Unit
) : RecyclerView.Adapter<StockAdapter.StockViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StockViewHolder {
        val itemView = LayoutInflater.from(parent.context).inflate(R.layout.item_product, parent, false)
        return StockViewHolder(itemView)
    }

    override fun onBindViewHolder(holder: StockViewHolder, position: Int) {
        val stock = stocks[position]
        holder.bind(stock)

        // Set onClickListener to navigate to ProductDetailActivity
        holder.buttonEditProduct.setOnClickListener {
            val intent = Intent(context, ProductDetailActivity::class.java)
            intent.putExtra("product_id", stock.productId.toString())
            context.startActivity(intent)
        }

        // Handle delete button click
        holder.buttonDeleteProduct.setOnClickListener {
            onDeleteClick(stock)
        }
    }

    override fun getItemCount() = stocks.size

    fun updateData(newStocks: List<Stock>) {
        this.stocks = newStocks
        notifyDataSetChanged()
    }

    inner class StockViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productName: TextView = itemView.findViewById(R.id.productName)
        private val productQuantity: TextView = itemView.findViewById(R.id.productQuantity)
        private val productVolume: TextView = itemView.findViewById(R.id.productVolume)
        val buttonEditProduct: ImageButton = itemView.findViewById(R.id.buttonEditProduct)
        val buttonDeleteProduct: ImageButton = itemView.findViewById(R.id.buttonDeleteProduct)

        fun bind(stock: Stock) {
            productName.text = stock.productName
            productQuantity.text = "Quantité : ${stock.quantity}"
            productVolume.text = "Volume : ${stock.volume} m³"
        }
    }
}
