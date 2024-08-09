package com.example.nomorewaste.api

import android.view.LayoutInflater
import android.view.View
import android.view.ViewGroup
import android.widget.TextView
import androidx.recyclerview.widget.RecyclerView
import com.example.nomorewaste.R

class RecipeAdapter(private val recipes: List<Recipe>) : RecyclerView.Adapter<RecipeAdapter.RecipeViewHolder>() {

    override fun onCreateViewHolder(parent: ViewGroup, viewType: Int): RecipeViewHolder {
        val view = LayoutInflater.from(parent.context).inflate(R.layout.item_recipe, parent, false)
        return RecipeViewHolder(view)
    }

    override fun onBindViewHolder(holder: RecipeViewHolder, position: Int) {
        val recipe = recipes[position]
        holder.bind(recipe)
    }

    override fun getItemCount(): Int = recipes.size

    class RecipeViewHolder(itemView: View) : RecyclerView.ViewHolder(itemView) {
        private val recipeName: TextView = itemView.findViewById(R.id.recipeName)
        private val recipeInstructions: TextView = itemView.findViewById(R.id.recipeInstructions)
        private val recipeIngredients: TextView = itemView.findViewById(R.id.recipeIngredients)

        fun bind(recipe: Recipe) {
            recipeName.text = recipe.name
            recipeInstructions.text = recipe.instructions
            recipeIngredients.text = recipe.ingredients.joinToString(separator = "\n") { ingredient ->
                "${ingredient.productName}: ${ingredient.quantityNeeded}"
            }
        }
    }
}
