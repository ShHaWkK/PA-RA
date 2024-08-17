package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.ArrayAdapter
import android.widget.Button
import android.widget.EditText
import android.widget.Spinner
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Warehouse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class UpdateProductActivity : AppCompatActivity() {

    private lateinit var editTextBarcode: EditText
    private lateinit var editTextName: EditText
    private lateinit var editTextExpirationDate: EditText
    private lateinit var editTextVolume: EditText
    private lateinit var spinnerWarehouse: Spinner
    private lateinit var buttonUpdateProduct: Button

    private lateinit var apiService: ApiService
    private var warehouses: List<Warehouse> = listOf()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_update_product)

        editTextBarcode = findViewById(R.id.editTextBarcode)
        editTextName = findViewById(R.id.editTextName)
        editTextExpirationDate = findViewById(R.id.editTextExpirationDate)
        editTextVolume = findViewById(R.id.editTextVolume)
        spinnerWarehouse = findViewById(R.id.spinnerWarehouse)
        buttonUpdateProduct = findViewById(R.id.buttonUpdateProduct)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadWarehouses()

        buttonUpdateProduct.setOnClickListener {
            val barcode = editTextBarcode.text.toString().trim()
            val name = editTextName.text.toString().trim()
            val expirationDate = editTextExpirationDate.text.toString().trim()
            val volume = editTextVolume.text.toString().trim().toFloatOrNull()
            val selectedWarehouse = warehouses[spinnerWarehouse.selectedItemPosition]

            if (barcode.isNotEmpty() && name.isNotEmpty() && expirationDate.isNotEmpty() && volume != null) {
                updateProduct(barcode, name, expirationDate, volume, selectedWarehouse.id)
            } else {
                Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadWarehouses() {
        apiService.getWarehouses().enqueue(object : Callback<List<Warehouse>> {
            override fun onResponse(call: Call<List<Warehouse>>, response: Response<List<Warehouse>>) {
                if (response.isSuccessful) {
                    warehouses = response.body() ?: listOf()
                    val warehouseNames = warehouses.map { it.name }
                    val adapter = ArrayAdapter(this@UpdateProductActivity, android.R.layout.simple_spinner_item, warehouseNames)
                    spinnerWarehouse.adapter = adapter
                } else {
                    Log.e("UpdateProductActivity", "Error loading warehouses: ${response.errorBody()?.string()}")
                    Toast.makeText(this@UpdateProductActivity, "Erreur de chargement des entrepôts", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Warehouse>>, t: Throwable) {
                Log.e("UpdateProductActivity", "Failure: ${t.message}", t)
                Toast.makeText(this@UpdateProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun updateProduct(barcode: String, name: String, expirationDate: String, volume: Float, warehouseId: Int) {
        val qrCodePath = "" // You might want to retrieve or generate the actual QR code path
        val product = Product(
            name = name,
            barcode = barcode,
            expirationDate = expirationDate,
            volume = volume,
            warehouseId = warehouseId,
            qrCodePath = qrCodePath
        )
        apiService.updateProduct(barcode, product).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UpdateProductActivity, "Produit mis à jour avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Log.e("UpdateProductActivity", "Error updating product: ${response.errorBody()?.string()}")
                    Toast.makeText(this@UpdateProductActivity, "Erreur lors de la mise à jour du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Log.e("UpdateProductActivity", "Failure: ${t.message}", t)
                Toast.makeText(this@UpdateProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
