package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.ImageView
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
import com.bumptech.glide.Glide
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ProductDetailActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var nameTextView: TextView
    private lateinit var barcodeTextView: TextView
    private lateinit var expirationDateTextView: TextView
    private lateinit var volumeTextView: TextView
    private lateinit var imageViewQRCode: ImageView
    private lateinit var buttonEditProduct: Button
    private lateinit var buttonDeleteProduct: Button

    private lateinit var product: Product

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_detail)

        nameTextView = findViewById(R.id.textViewProductName)
        barcodeTextView = findViewById(R.id.textViewProductBarcode)
        expirationDateTextView = findViewById(R.id.textViewExpirationDate)
        volumeTextView = findViewById(R.id.textViewVolume)
        imageViewQRCode = findViewById(R.id.imageViewQRCode)
        buttonEditProduct = findViewById(R.id.buttonEditProduct)
        buttonDeleteProduct = findViewById(R.id.buttonDeleteProduct)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        val productId = intent.getStringExtra("product_id") ?: return

        loadProductDetails(productId)

        buttonEditProduct.setOnClickListener {
            val intent = Intent(this, EditProductActivity::class.java)
            intent.putExtra("product_id", product.barcode) // Pass barcode as identifier to EditProductActivity
            startActivity(intent)
        }

        buttonDeleteProduct.setOnClickListener {
            deleteProduct(productId)
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
                    finish() // Close the activity if the product is not found
                }
            }

            override fun onFailure(call: Call<Product>, t: Throwable) {
                finish() // Close the activity on failure
            }
        })
    }

    private fun populateProductDetails(product: Product) {
        nameTextView.text = product.name
        barcodeTextView.text = product.barcode
        expirationDateTextView.text = product.expirationDate
        volumeTextView.text = product.volume.toString()

        // Load the QR code image using Glide or another image loading library
        Glide.with(this)
            .load(product.qrCodePath)
            .placeholder(R.drawable.placeholder_qr_code) // Optional placeholder
            .into(imageViewQRCode)
    }

    private fun deleteProduct(productId: String) {
        apiService.deleteProduct(productId).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    finish()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {}
        })
    }
}
