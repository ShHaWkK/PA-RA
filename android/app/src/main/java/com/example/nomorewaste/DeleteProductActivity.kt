package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class DeleteProductActivity : AppCompatActivity() {

    private lateinit var editTextBarcode: EditText
    private lateinit var buttonDeleteProduct: Button

    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_delete_product)

        editTextBarcode = findViewById(R.id.editTextBarcode)
        buttonDeleteProduct = findViewById(R.id.buttonDeleteProduct)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        buttonDeleteProduct.setOnClickListener {
            val barcode = editTextBarcode.text.toString().trim()
            if (barcode.isNotEmpty()) {
                deleteProduct(barcode)
            } else {
                Toast.makeText(this, "Veuillez entrer le code-barres du produit", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun deleteProduct(barcode: String) {
        apiService.deleteProduct(barcode).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@DeleteProductActivity, "Produit supprimé avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Log.e("DeleteProductActivity", "Error deleting product: ${response.errorBody()?.string()}")
                    Toast.makeText(this@DeleteProductActivity, "Erreur lors de la suppression du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Log.e("DeleteProductActivity", "Failure: ${t.message}", t)
                Toast.makeText(this@DeleteProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
