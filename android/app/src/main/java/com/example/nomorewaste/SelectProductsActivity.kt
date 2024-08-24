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

    private lateinit var recyclerViewProducts: RecyclerView
    private lateinit var buttonConfirmSelection: Button
    private lateinit var apiService: ApiService
    private lateinit var productAdapter: ProductAdapter

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_selects_products)

        recyclerViewProducts = findViewById(R.id.recyclerViewProducts)
        buttonConfirmSelection = findViewById(R.id.buttonConfirmSelection)

        recyclerViewProducts.layoutManager = LinearLayoutManager(this)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        loadProducts()

        buttonConfirmSelection.setOnClickListener {
            val selectedProducts = productAdapter.getSelectedProducts()
            if (selectedProducts.isEmpty()) {
                Toast.makeText(this, "Aucun produit sélectionné", Toast.LENGTH_SHORT).show()
            } else {
                suggestMenu(selectedProducts)
            }
        }
    }

    private fun loadProducts() {
        apiService.getProducts().enqueue(object : Callback<List<Product>> {
            override fun onResponse(call: Call<List<Product>>, response: Response<List<Product>>) {
                if (response.isSuccessful) {
                    val products = response.body() ?: emptyList()
                    productAdapter = ProductAdapter(products,
                        onEditClick = {}, // Pas besoin d'implémenter pour cette activité
                        onDeleteClick = {} // Pas besoin d'implémenter pour cette activité
                    )
                    recyclerViewProducts.adapter = productAdapter
                } else {
                    Toast.makeText(this@SelectProductsActivity, "Erreur lors du chargement des produits", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Product>>, t: Throwable) {
                Toast.makeText(this@SelectProductsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun suggestMenu(selectedProducts: List<Product>) {
        // Créer une map des produits par code-barres, en utilisant !! pour forcer le non-null
        val productsMap = selectedProducts.associateBy { it.barcode!! }

        // Créer une requête SuggestRecipesRequest avec la map des produits
        val request = SuggestRecipesRequest(productsMap)

        apiService.suggestRecipes(request).enqueue(object : Callback<List<Recipe>> {
            override fun onResponse(call: Call<List<Recipe>>, response: Response<List<Recipe>>) {
                if (response.isSuccessful) {
                    val suggestedRecipes = response.body() ?: emptyList()
                    showSuggestedRecipes(suggestedRecipes)
                } else {
                    Toast.makeText(this@SelectProductsActivity, "Erreur lors de la suggestion du menu", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Recipe>>, t: Throwable) {
                Toast.makeText(this@SelectProductsActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }


    private fun showSuggestedRecipes(recipes: List<Recipe>) {
        val intent = Intent(this, SuggestMenuActivity::class.java)
        intent.putParcelableArrayListExtra("recipes", ArrayList(recipes))
        startActivity(intent)
    }
}
