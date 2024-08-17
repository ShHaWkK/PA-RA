package com.example.nomorewaste

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.ImageView
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.bumptech.glide.Glide
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class EditProductActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var nameEditText: EditText
    private lateinit var barcodeTextView: EditText
    private lateinit var expirationDateEditText: EditText
    private lateinit var volumeEditText: EditText
    private lateinit var imageViewQRCode: ImageView
    private lateinit var buttonSaveChanges: Button

    private lateinit var product: Product

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_edit_product)

        nameEditText = findViewById(R.id.editTextProductName)
        barcodeTextView = findViewById(R.id.textViewProductBarcode) // Set to TextView since it's non-editable
        expirationDateEditText = findViewById(R.id.editTextExpirationDate)
        volumeEditText = findViewById(R.id.editTextVolume)
        imageViewQRCode = findViewById(R.id.imageViewQRCode)
        buttonSaveChanges = findViewById(R.id.buttonSaveChanges)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        val productId = intent.getStringExtra("product_id") ?: return

        loadProductDetails(productId)

        buttonSaveChanges.setOnClickListener {
            saveProductChanges()
        }
    }

    private fun loadProductDetails(productId: String) {
        apiService.getProduct(productId).enqueue(object : Callback<Product> {
            override fun onResponse(call: Call<Product>, response: Response<Product>) {
                if (response.isSuccessful) {
                    response.body()?.let {
                        product = it
                        populateProductDetails(it)
                    }
                } else {
                    Toast.makeText(this@EditProductActivity, "Erreur: Produit non trouvé", Toast.LENGTH_SHORT).show()
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
        barcodeTextView.setText(product.barcode) // Display barcode as non-editable
        expirationDateEditText.setText(product.expirationDate)
        volumeEditText.setText(product.volume.toString())

        // Load the QR code image using Glide or another image loading library
        Glide.with(this)
            .load(product.qrCodePath)
            .placeholder(R.drawable.placeholder_qr_code)  // Optional placeholder
            .into(imageViewQRCode)
    }

    private fun saveProductChanges() {
        val updatedProduct = product.copy(
            name = nameEditText.text.toString(),
            expirationDate = expirationDateEditText.text.toString(),
            volume = volumeEditText.text.toString().toFloat()
        )

        // Call the API to update the product
        apiService.updateProduct(updatedProduct.barcode!!, updatedProduct).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@EditProductActivity, "Produit mis à jour avec succès", Toast.LENGTH_SHORT).show()
                    finish() // Close the activity
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
