package com.example.nomorewaste

import android.os.Bundle
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Warehouse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class EditProductActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var warehouseSpinner: Spinner
    private lateinit var warehouses: List<Warehouse>
    private var selectedWarehouseId: Int? = null
    private lateinit var nameEditText: EditText
    private lateinit var barcodeEditText: EditText
    private lateinit var expirationDateEditText: EditText
    private lateinit var volumeEditText: EditText
    private lateinit var saveButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_product)

        nameEditText = findViewById(R.id.editTextProductName)
        barcodeEditText = findViewById(R.id.editTextProductBarcode)
        expirationDateEditText = findViewById(R.id.editTextExpirationDate)
        volumeEditText = findViewById(R.id.editTextVolume)
        saveButton = findViewById(R.id.buttonSaveProduct)
        warehouseSpinner = findViewById(R.id.spinnerWarehouse)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        val productBarcode: String? = intent.getStringExtra("product_id")
        if (productBarcode == null) {
            Toast.makeText(this, "Erreur: Produit non trouvé", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        loadWarehouses()
        loadProductDetails(productBarcode)

        saveButton.setOnClickListener {
            val name = nameEditText.text.toString().trim()
            val barcode = barcodeEditText.text.toString().trim()
            val expirationDate = expirationDateEditText.text.toString().trim()
            val volume = volumeEditText.text.toString().trim().toFloatOrNull()

            if (name.isEmpty() || barcode.isEmpty() || expirationDate.isEmpty() || volume == null || selectedWarehouseId == null) {
                Toast.makeText(this, "Tous les champs sont requis", Toast.LENGTH_SHORT).show()
            } else {
                val updatedProduct = Product(name, barcode, expirationDate, volume, selectedWarehouseId!!)
                updateProduct(productBarcode, updatedProduct)
            }
        }
    }

    private fun loadWarehouses() {
        apiService.getWarehouses().enqueue(object : Callback<List<Warehouse>> {
            override fun onResponse(call: Call<List<Warehouse>>, response: Response<List<Warehouse>>) {
                if (response.isSuccessful) {
                    warehouses = response.body() ?: emptyList()
                    val warehouseNames = warehouses.map { it.name }
                    val adapter = ArrayAdapter(this@EditProductActivity, android.R.layout.simple_spinner_item, warehouseNames)
                    adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
                    warehouseSpinner.adapter = adapter
                    warehouseSpinner.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
                        override fun onItemSelected(parent: AdapterView<*>, view: View, position: Int, id: Long) {
                            selectedWarehouseId = warehouses[position].id
                        }

                        override fun onNothingSelected(parent: AdapterView<*>) {
                            selectedWarehouseId = null
                        }
                    }
                } else {
                    Toast.makeText(this@EditProductActivity, "Erreur lors du chargement des entrepôts", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Warehouse>>, t: Throwable) {
                Toast.makeText(this@EditProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun loadProductDetails(barcode: String) {
        apiService.getProduct(barcode).enqueue(object : Callback<Product> {
            override fun onResponse(call: Call<Product>, response: Response<Product>) {
                if (response.isSuccessful) {
                    val product = response.body()
                    if (product != null) {
                        populateProductDetails(product)
                    } else {
                        Toast.makeText(this@EditProductActivity, "Produit non trouvé", Toast.LENGTH_SHORT).show()
                        finish()
                    }
                } else {
                    Toast.makeText(this@EditProductActivity, "Erreur lors du chargement du produit", Toast.LENGTH_SHORT).show()
                    finish()
                }
            }

            override fun onFailure(call: Call<Product>, t: Throwable) {
                Toast.makeText(this@EditProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                finish()
            }
        })
    }

    private fun populateProductDetails(product: Product) {
        nameEditText.setText(product.name)
        barcodeEditText.setText(product.barcode)
        expirationDateEditText.setText(product.expirationDate)
        volumeEditText.setText(product.volume.toString())

        selectedWarehouseId = product.warehouseId
        warehouseSpinner.setSelection(warehouses.indexOfFirst { it.id == product.warehouseId })
    }

    private fun updateProduct(barcode: String, updatedProduct: Product) {
        apiService.updateProduct(barcode, updatedProduct).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@EditProductActivity, "Produit mis à jour avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@EditProductActivity, "Erreur lors de la mise à jour du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@EditProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
