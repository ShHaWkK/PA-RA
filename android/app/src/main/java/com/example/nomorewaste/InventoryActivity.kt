package com.example.nomorewaste

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.ProductAdapter
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class InventoryActivity : AppCompatActivity() {

    private lateinit var editTextProductName: EditText
    private lateinit var editTextProductBarcode: EditText
    private lateinit var editTextQuantity: EditText
    private lateinit var editTextExpirationDate: EditText
    private lateinit var buttonAddProduct: Button
    private lateinit var recyclerViewProducts: RecyclerView
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_inventory)

        editTextProductName = findViewById(R.id.editTextProductName)
        editTextProductBarcode = findViewById(R.id.editTextProductBarcode)
        editTextQuantity = findViewById(R.id.editTextQuantity)
        editTextExpirationDate = findViewById(R.id.editTextExpirationDate)
        buttonAddProduct = findViewById(R.id.buttonAddProduct)
        recyclerViewProducts = findViewById(R.id.recyclerViewProducts)

        recyclerViewProducts.layoutManager = LinearLayoutManager(this)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        buttonAddProduct.setOnClickListener {
            val name = editTextProductName.text.toString().trim()
            val barcode = editTextProductBarcode.text.toString().trim()
            val quantity = editTextQuantity.text.toString().trim().toIntOrNull()
            val expirationDate = editTextExpirationDate.text.toString().trim()

            if (name.isNotEmpty() && barcode.isNotEmpty() && quantity != null && expirationDate.isNotEmpty()) {
                val product = Product(name, barcode, expirationDate, volume = quantity.toFloat(), warehouseId = 1) // Simplification : warehouseId = 1
                addProduct(product)
            } else {
                Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            }
        }

        loadProducts()
    }

    private fun addProduct(product: Product) {
        apiService.addProduct(product).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@InventoryActivity, "Produit ajouté avec succès", Toast.LENGTH_SHORT).show()
                    loadProducts()
                } else {
                    Toast.makeText(this@InventoryActivity, "Erreur lors de l'ajout du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@InventoryActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun loadProducts() {
        apiService.getProducts().enqueue(object : Callback<List<Product>> {
            override fun onResponse(call: Call<List<Product>>, response: Response<List<Product>>) {
                if (response.isSuccessful) {
                    recyclerViewProducts.adapter = ProductAdapter(
                        response.body() ?: listOf(),
                        onEditClick = { product -> editProduct(product) },
                        onDeleteClick = { product -> deleteProduct(product) }
                    )
                } else {
                    Toast.makeText(this@InventoryActivity, "Erreur lors du chargement des produits", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Product>>, t: Throwable) {
                Toast.makeText(this@InventoryActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun editProduct(product: Product) {
        // Logic to edit the product
        Toast.makeText(this, "Edit: ${product.name}", Toast.LENGTH_SHORT).show()
        // You might want to start an activity to edit the product
    }

    private fun deleteProduct(product: Product) {
        val barcode = product.barcode ?: ""
        if (barcode.isEmpty()) {
            Toast.makeText(this, "Erreur: Code-barres non disponible", Toast.LENGTH_SHORT).show()
            return
        }

        apiService.deleteProduct(barcode).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@InventoryActivity, "Produit supprimé avec succès", Toast.LENGTH_SHORT).show()
                    loadProducts()
                } else {
                    Toast.makeText(this@InventoryActivity, "Erreur lors de la suppression du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@InventoryActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

}
