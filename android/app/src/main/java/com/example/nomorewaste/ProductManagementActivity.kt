package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
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
    private lateinit var warehouseMap: Map<String, Int>
    private lateinit var buttonAddProduct: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_management)

        filterSpinner = findViewById(R.id.filterSpinner)
        recyclerViewStocks = findViewById(R.id.recyclerViewProducts)
        buttonAddProduct = findViewById(R.id.buttonAddProduct)

        recyclerViewStocks.layoutManager = LinearLayoutManager(this)
        stockAdapter = StockAdapter(listOf())
        recyclerViewStocks.adapter = stockAdapter

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadWarehouses()

        filterSpinner.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>, view: View?, position: Int, id: Long) {
                val selectedWarehouseName = parent.getItemAtPosition(position) as String
                loadStocksByWarehouse(selectedWarehouseName)
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
                    warehouseMap = warehouses.associate { it.name to it.id }
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

    private fun loadStocksByWarehouse(warehouseName: String) {
        val warehouseId = warehouseMap[warehouseName]
        if (warehouseId != null) {
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
        } else {
            Toast.makeText(this, "Entrepôt non trouvé", Toast.LENGTH_SHORT).show()
        }
    }

    private fun onAddProductClicked() {
        // Lancer l'activité pour ajouter un nouveau produit
        val intent = Intent(this, AddProductActivity::class.java)
        startActivity(intent)
    }
}
