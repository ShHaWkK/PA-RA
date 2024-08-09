package com.example.nomorewaste

import android.os.Bundle
import android.view.View
import android.widget.AdapterView
import android.widget.Spinner
import androidx.appcompat.app.AppCompatActivity
import androidx.recyclerview.widget.LinearLayoutManager
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.api.Recipe
import com.example.nomorewaste.api.RecipeAdapter

class SuggestMenuActivity : AppCompatActivity() {

    private lateinit var recyclerViewSuggestedRecipes: RecyclerView
    private lateinit var spinnerFilter: Spinner
    private lateinit var recipeAdapter: RecipeAdapter
    private var recipes: List<Recipe> = listOf()

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_suggest_menu)

        recyclerViewSuggestedRecipes = findViewById(R.id.recyclerViewSuggestedRecipes)
        spinnerFilter = findViewById(R.id.spinnerFilter)
        recyclerViewSuggestedRecipes.layoutManager = LinearLayoutManager(this)

        recipeAdapter = RecipeAdapter(recipes)
        recyclerViewSuggestedRecipes.adapter = recipeAdapter

        // Get recipes passed from the previous activity
        recipes = intent.getSerializableExtra("recipes") as List<Recipe> // Cast as Serializable list
        recipeAdapter.updateData(recipes)

        spinnerFilter.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>?, view: View?, position: Int, id: Long) {
                applyFilter(parent?.getItemAtPosition(position) as String)
            }

            override fun onNothingSelected(parent: AdapterView<*>?) {}
        }
    }

    private fun applyFilter(filter: String) {
        val filteredRecipes = if (filter == "All") {
            recipes
        } else {
            recipes.filter { it.tags?.contains(filter) == true }
        }
        recipeAdapter.updateData(filteredRecipes)
    }
}
