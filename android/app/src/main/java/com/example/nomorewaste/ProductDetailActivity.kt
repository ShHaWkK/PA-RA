package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.TextView
import androidx.appcompat.app.AppCompatActivity
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
    private lateinit var buttonEditProduct: Button
    private lateinit var buttonDeleteProduct: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_product_detail)

        nameTextView = findViewById(R.id.textViewProductName)
        barcodeTextView = findViewById(R.id.textViewProductBarcode)
        expirationDateTextView = findViewById(R.id.textViewExpirationDate)
        volumeTextView = findViewById(R.id.textViewVolume)
        buttonEditProduct = findViewById(R.id.buttonEditProduct)
        buttonDeleteProduct = findViewById(R.id.buttonDeleteProduct)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        val productBarcode = intent.getStringExtra("product_id") ?: return

        loadProductDetails(productBarcode)

        buttonEditProduct.setOnClickListener {
            val intent = Intent(this, EditProductActivity::class.java).apply {
                putExtra("product_id", productBarcode)
            }
            startActivity(intent)
        }

        buttonDeleteProduct.setOnClickListener {
            deleteProduct(productBarcode)
        }
    }

    private fun loadProductDetails(barcode: String) {
        apiService.getProduct(barcode).enqueue(object : Callback<Product> {
            override fun onResponse(call: Call<Product>, response: Response<Product>) {
                if (response.isSuccessful) {
                    response.body()?.let { product ->
                        nameTextView.text = product.name
                        barcodeTextView.text = product.barcode
                        expirationDateTextView.text = product.expirationDate
                        volumeTextView.text = product.volume.toString()
                    }
                } else {
                    finish()
                }
            }

            override fun onFailure(call: Call<Product>, t: Throwable) {
                finish()
            }
        })
    }

    private fun deleteProduct(barcode: String) {
        apiService.deleteProduct(barcode).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    finish()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {}
        })
    }
}
