package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.ProductAdapter
import com.example.nomorewaste.api.Recipe
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.SuggestRecipesRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class SelectProductsActivity : AppCompatActivity() {

    private lateinit var buttonSuggestMenu: Button
    private lateinit var recyclerViewProducts: RecyclerView
    private lateinit var apiService: ApiService
    private lateinit var productAdapter: ProductAdapter
    private var products: List<Product> = listOf()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_select_products)

        buttonSuggestMenu = findViewById(R.id.buttonSuggestMenu)
        recyclerViewProducts = findViewById(R.id.recyclerViewProducts)
        recyclerViewProducts.layoutManager = LinearLayoutManager(this)

        productAdapter = ProductAdapter(products)
        recyclerViewProducts.adapter = productAdapter

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadProducts()

        buttonSuggestMenu.setOnClickListener {
            suggestMenu()
        }
    }

    private fun loadProducts() {
        apiService.getProductsInStock().enqueue(object : Callback<Map<String, Product>> {
            override fun onResponse(call: Call<Map<String, Product>>, response: Response<Map<String, Product>>) {
                if (response.isSuccessful) {
                    val productsInStock = response.body()?.values?.toList() ?: emptyList()
                    productAdapter.updateData(productsInStock)
                } else {
                    Toast.makeText(this@SelectProductsActivity, getString(R.string.error_fetching_products), Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Map<String, Product>>, t: Throwable) {
                Toast.makeText(this@SelectProductsActivity, getString(R.string.connection_failure, t.message), Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun suggestMenu() {
        val selectedProducts = productAdapter.getSelectedProducts()
        if (selectedProducts.isEmpty()) {
            Toast.makeText(this, getString(R.string.select_product_warning), Toast.LENGTH_SHORT).show()
            return
        }

        val selectedProductsMap = selectedProducts
            .filter { it.barcode != null }
            .associate { it.barcode!! to it.volume.toInt() }

        if (selectedProductsMap.isEmpty()) {
            Toast.makeText(this, getString(R.string.select_product_warning), Toast.LENGTH_SHORT).show()
            return
        }

        val requestBody = SuggestRecipesRequest(selectedProductsMap)
        apiService.suggestRecipes(requestBody).enqueue(object : Callback<List<Recipe>> {
            override fun onResponse(call: Call<List<Recipe>>, response: Response<List<Recipe>>) {
                if (response.isSuccessful) {
                    var recipes = response.body() ?: listOf()

                    // Filtrer les recettes dont les ingrédients manquants ne sont pas logiques
                    recipes = recipes.filter { recipe ->
                        recipe.missingIngredients.all { missingIngredient ->
                            selectedProductsMap.containsKey(missingIngredient.productName)
                        }
                    }

                    if (recipes.isNotEmpty()) {
                        val intent = Intent(this@SelectProductsActivity, SuggestMenuActivity::class.java)
                        intent.putParcelableArrayListExtra("recipes", ArrayList(recipes))
                        startActivity(intent)
                    } else {
                        Toast.makeText(this@SelectProductsActivity, getString(R.string.no_recipes_found), Toast.LENGTH_SHORT).show()
                    }
                } else {
                    Toast.makeText(this@SelectProductsActivity, getString(R.string.suggestion_error), Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Recipe>>, t: Throwable) {
                Toast.makeText(this@SelectProductsActivity, getString(R.string.connection_failure, t.message), Toast.LENGTH_SHORT).show()
            }
        })
    }


}
