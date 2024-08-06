// SuggestMenuActivity.kt
package com.example.nomorewaste

import android.os.Bundle
import android.widget.Button
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Recipe
import com.example.nomorewaste.api.RecipeAdapter
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class SuggestMenuActivity : AppCompatActivity() {

    private lateinit var buttonSuggestMenu: Button
    private lateinit var recyclerViewSuggestedRecipes: RecyclerView
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_suggest_menu)

        buttonSuggestMenu = findViewById(R.id.buttonSuggestMenu)
        recyclerViewSuggestedRecipes = findViewById(R.id.recyclerViewSuggestedRecipes)
        recyclerViewSuggestedRecipes.layoutManager = LinearLayoutManager(this)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        buttonSuggestMenu.setOnClickListener {
            suggestMenu()
        }
    }

    private fun suggestMenu() {
        apiService.getSuggestedRecipes().enqueue(object : Callback<List<Recipe>> {
            override fun onResponse(call: Call<List<Recipe>>, response: Response<List<Recipe>>) {
                if (response.isSuccessful) {
                    recyclerViewSuggestedRecipes.adapter = RecipeAdapter(response.body() ?: listOf())
                } else {
                    Toast.makeText(this@SuggestMenuActivity, "Erreur lors de la suggestion des recettes", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Recipe>>, t: Throwable) {
                Toast.makeText(this@SuggestMenuActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
