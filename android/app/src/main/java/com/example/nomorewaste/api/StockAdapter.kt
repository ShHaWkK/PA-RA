package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class StockAdapter(private var stockList: List<Stock>) :
    RecyclerView.Adapter<StockAdapter.StockViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): StockViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_stock, parent, false)
        return StockViewHolder(view)
    }

    override fun onBindViewHolder(holder: StockViewHolder, position: Int) {
        val stock = stockList[position]
        holder.bind(stock)
    }

    override fun getItemCount(): Int = stockList.size

    fun updateData(newStocks: List<Stock>) {
        stockList = newStocks
        notifyDataSetChanged()
    }

    inner class StockViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val productName: TextView = itemView.findViewById(R.id.productName)
        private val productQuantity: TextView = itemView.findViewById(R.id.productQuantity)
        private val productVolume: TextView = itemView.findViewById(R.id.productVolume)

        fun bind(stock: Stock) {
            productName.text = stock.productName ?: "Nom non disponible"
            productQuantity.text = "Quantité : ${stock.quantity}"
            productVolume.text = "Volume : ${stock.volume} m³"
        }
    }
}
