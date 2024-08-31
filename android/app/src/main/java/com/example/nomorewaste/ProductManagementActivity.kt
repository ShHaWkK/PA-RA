package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.CapacityData
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Stock
import com.example.nomorewaste.api.StockAdapter
import com.example.nomorewaste.api.Warehouse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProductManagementActivity : AppCompatActivity() {

    private lateinit var filterSpinner: Spinner
    private lateinit var recyclerViewStocks: RecyclerView
    private lateinit var apiService: ApiService
    private lateinit var stockAdapter: StockAdapter
    private lateinit var warehouseMap: Map<String, Warehouse>
    private lateinit var warehouseCapacityTextView: TextView
    private lateinit var progressBarCapacity: ProgressBar
    private lateinit var buttonAddProduct: Button
    private var selectedWarehouseId: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_management)

        filterSpinner = findViewById(R.id.filterSpinner)
        recyclerViewStocks = findViewById(R.id.recyclerViewProducts)
        warehouseCapacityTextView = findViewById(R.id.warehouseCapacityTextView)
        progressBarCapacity = findViewById(R.id.progressBarCapacity)
        buttonAddProduct = findViewById(R.id.buttonAddProduct)

        recyclerViewStocks.layoutManager = LinearLayoutManager(this)
        stockAdapter = StockAdapter(
            listOf(),
            context = this,
            onDeleteClick = { stock ->
                deleteProduct(stock)
            }
        )

        recyclerViewStocks.adapter = stockAdapter

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadWarehouses()

        filterSpinner.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>, view: View?, position: Int, id: Long) {
                val selectedWarehouseName = parent.getItemAtPosition(position) as String
                val selectedWarehouse = warehouseMap[selectedWarehouseName]
                selectedWarehouseId = selectedWarehouse?.id
                if (selectedWarehouse != null) {
                    loadStocksByWarehouse(selectedWarehouse.id)
                    updateCapacityProgress(selectedWarehouse)
                }
            }

            override fun onNothingSelected(parent: AdapterView<*>) {}
        }

        buttonAddProduct.setOnClickListener {
            onAddProductClicked()
        }
    }

    private fun loadWarehouses() {
        apiService.getWarehouses().enqueue(object : Callback<List<Warehouse>> {
            override fun onResponse(call: Call<List<Warehouse>>, response: Response<List<Warehouse>>) {
                if (response.isSuccessful) {
                    val warehouses = response.body() ?: listOf()
                    warehouseMap = warehouses.associateBy { it.name }
                    val warehouseNames = warehouseMap.keys.toList()
                    val adapter = ArrayAdapter(this@ProductManagementActivity, android.R.layout.simple_spinner_item, warehouseNames)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    filterSpinner.adapter = adapter
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur de chargement des entrepôts", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Warehouse>>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun loadStocksByWarehouse(warehouseId: Int) {
        apiService.getStocksByWarehouse(warehouseId).enqueue(object : Callback<List<Stock>> {
            override fun onResponse(call: Call<List<Stock>>, response: Response<List<Stock>>) {
                if (response.isSuccessful) {
                    val stocks = response.body() ?: listOf()
                    stockAdapter.updateData(stocks)
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur de chargement des stocks", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Stock>>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateCapacityProgress(warehouse: Warehouse) {
        apiService.getWarehouseCapacity(warehouse.id).enqueue(object : Callback<CapacityData> {
            override fun onResponse(call: Call<CapacityData>, response: Response<CapacityData>) {
                if (response.isSuccessful) {
                    val capacityData = response.body()
                    if (capacityData != null) {
                        try {
                            val percentage = (capacityData.occupiedCapacity.toFloat() / capacityData.totalCapacity.toFloat()) * 100
                            progressBarCapacity.progress = percentage.toInt()
                            progressBarCapacity.visibility = View.VISIBLE
                            warehouseCapacityTextView.text = "Warehouse Capacity: ${"%.2f".format(percentage)}%"
                            updateProgressBarColor(percentage)
                        } catch (e: NumberFormatException) {
                            e.printStackTrace()
                            Toast.makeText(this@ProductManagementActivity, "Invalid capacity data received", Toast.LENGTH_SHORT).show()
                        }
                    }
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur de chargement de la capacité", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<CapacityData>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateProgressBarColor(percentage: Float) {
        val color = when {
            percentage < 50 -> ContextCompat.getColor(this, R.color.green)
            percentage < 75 -> ContextCompat.getColor(this, R.color.yellow)
            percentage < 90 -> ContextCompat.getColor(this, R.color.orange)
            percentage < 100 -> ContextCompat.getColor(this, R.color.red)
            else -> ContextCompat.getColor(this, R.color.dark_red)
        }
        progressBarCapacity.progressDrawable.setColorFilter(color, android.graphics.PorterDuff.Mode.SRC_IN)
    }

    private fun onAddProductClicked() {
        val intent = Intent(this, AddProductActivity::class.java)
        startActivity(intent)
    }

    private fun deleteProduct(stock: Stock) {
        val productId = stock.id.toString()
        apiService.deleteProduct(productId).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ProductManagementActivity, "Produit supprimé avec succès", Toast.LENGTH_SHORT).show()
                    selectedWarehouseId?.let { loadStocksByWarehouse(it) }
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur lors de la suppression du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
