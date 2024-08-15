package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.ProductAdapter
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Warehouse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProductManagementActivity : AppCompatActivity() {

    private lateinit var editTextProductName: EditText
    private lateinit var editTextProductBarcode: EditText
    private lateinit var editTextQuantity: EditText
    private lateinit var editTextExpirationDate: EditText
    private lateinit var spinnerWarehouse: Spinner
    private lateinit var buttonAddProduct: Button
    private lateinit var buttonUpdateProduct: Button
    private lateinit var buttonDeleteProduct: Button
    private lateinit var recyclerViewProducts: RecyclerView

    private lateinit var apiService: ApiService
    private var warehouses: List<Warehouse> = listOf()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_management)

        editTextProductName = findViewById(R.id.editTextProductName)
        editTextProductBarcode = findViewById(R.id.editTextProductBarcode)
        editTextQuantity = findViewById(R.id.editTextQuantity)
        editTextExpirationDate = findViewById(R.id.editTextExpirationDate)
        spinnerWarehouse = findViewById(R.id.spinnerWarehouse)
        buttonAddProduct = findViewById(R.id.buttonAddProduct)
        buttonUpdateProduct = findViewById(R.id.buttonUpdateProduct)
        buttonDeleteProduct = findViewById(R.id.buttonDeleteProduct)
        recyclerViewProducts = findViewById(R.id.recyclerViewProducts)

        recyclerViewProducts.layoutManager = LinearLayoutManager(this)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        loadWarehouses()
        loadProducts()

        buttonAddProduct.setOnClickListener {
            val name = editTextProductName.text.toString().trim()
            val barcode = editTextProductBarcode.text.toString().trim()
            val quantity = editTextQuantity.text.toString().trim().toIntOrNull()
            val expirationDate = editTextExpirationDate.text.toString().trim()
            val selectedWarehouse = warehouses[spinnerWarehouse.selectedItemPosition]

            if (name.isNotEmpty() && barcode.isNotEmpty() && quantity != null && expirationDate.isNotEmpty()) {
                addProduct(name, barcode, quantity, expirationDate, selectedWarehouse.id)
            } else {
                Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            }
        }

        buttonUpdateProduct.setOnClickListener {
            val name = editTextProductName.text.toString().trim()
            val barcode = editTextProductBarcode.text.toString().trim()
            val quantity = editTextQuantity.text.toString().trim().toIntOrNull()
            val expirationDate = editTextExpirationDate.text.toString().trim()
            val selectedWarehouse = warehouses[spinnerWarehouse.selectedItemPosition]

            if (name.isNotEmpty() && barcode.isNotEmpty() && quantity != null && expirationDate.isNotEmpty()) {
                updateProduct(name, barcode, quantity, expirationDate, selectedWarehouse.id)
            } else {
                Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            }
        }

        buttonDeleteProduct.setOnClickListener {
            val barcode = editTextProductBarcode.text.toString().trim()
            if (barcode.isNotEmpty()) {
                deleteProduct(barcode)
            } else {
                Toast.makeText(this, "Veuillez entrer le code-barres du produit", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadWarehouses() {
        apiService.getWarehouses().enqueue(object : Callback<List<Warehouse>> {
            override fun onResponse(call: Call<List<Warehouse>>, response: Response<List<Warehouse>>) {
                if (response.isSuccessful) {
                    warehouses = response.body() ?: listOf()
                    val warehouseNames = warehouses.map { it.name }
                    val adapter = ArrayAdapter(this@ProductManagementActivity, android.R.layout.simple_spinner_item, warehouseNames)
                    spinnerWarehouse.adapter = adapter
                } else {
                    Log.e("ProductManagement", "Error loading warehouses: ${response.errorBody()?.string()}")
                    Toast.makeText(this@ProductManagementActivity, "Erreur de chargement des entrepôts", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Warehouse>>, t: Throwable) {
                Log.e("ProductManagement", "Failure: ${t.message}", t)
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun loadProducts() {
        apiService.getProducts().enqueue(object : Callback<List<Product>> {
            override fun onResponse(call: Call<List<Product>>, response: Response<List<Product>>) {
                if (response.isSuccessful) {
                    recyclerViewProducts.adapter = ProductAdapter(response.body() ?: listOf())
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur lors du chargement des produits", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Product>>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun addProduct(name: String, barcode: String, quantity: Int, expirationDate: String, warehouseId: Int) {
        val product = Product(name, barcode, expirationDate, quantity.toFloat(), warehouseId, false)
        apiService.addProduct(product).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ProductManagementActivity, "Produit ajouté avec succès", Toast.LENGTH_SHORT).show()
                    loadProducts()
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur lors de l'ajout du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateProduct(name: String, barcode: String, quantity: Int, expirationDate: String, warehouseId: Int) {
        val product = Product(name, barcode, expirationDate, quantity.toFloat(), warehouseId, false)
        apiService.updateProduct(barcode, product).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ProductManagementActivity, "Produit mis à jour avec succès", Toast.LENGTH_SHORT).show()
                    loadProducts()
                } else {
                    Toast.makeText(this@ProductManagementActivity, "Erreur lors de la mise à jour du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@ProductManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun deleteProduct(barcode: String) {
        apiService.deleteProduct(barcode).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ProductManagementActivity, "Produit supprimé avec succès", Toast.LENGTH_SHORT).show()
                    loadProducts()
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
