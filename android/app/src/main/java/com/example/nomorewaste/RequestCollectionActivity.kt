package com.example.nomorewaste

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.*
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.viewmodel.CollectionViewModel
import java.util.*

class RequestCollectionActivity : AppCompatActivity() {

    private val collectionViewModel: CollectionViewModel by viewModels()
    private lateinit var productSpinner: Spinner
    private lateinit var dateEditText: EditText
    private lateinit var addressEditText: EditText
    private lateinit var submitButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_request_collection)

        productSpinner = findViewById(R.id.spinner_product)
        dateEditText = findViewById(R.id.edit_text_date)
        addressEditText = findViewById(R.id.edit_text_address)
        submitButton = findViewById(R.id.button_submit_request)

        // Charger la liste des produits dans le Spinner
        loadProductsIntoSpinner()

        // Configurer le DatePicker pour la sélection de la date
        setupDatePicker()

        // Récupération de l'ID de l'entreprise à partir de SharedPreferences
        val sharedPreferences = getSharedPreferences("user_prefs", MODE_PRIVATE)
        val companyId = sharedPreferences.getInt("company_id", -1)

        submitButton.setOnClickListener {
            val product = productSpinner.selectedItem as? String
            val date = dateEditText.text.toString()
            val address = addressEditText.text.toString()

            if (product != null && date.isNotEmpty() && address.isNotEmpty() && companyId != -1) {
                val requestData = mapOf(
                    "product" to product,
                    "collection_date" to date,
                    "address" to address,
                    "company_id" to companyId
                )

                collectionViewModel.createCollectionRequest(requestData)

                collectionViewModel.error.observe(this) { error ->
                    if (error != null) {
                        Toast.makeText(this, "Error: $error", Toast.LENGTH_SHORT).show()
                    } else {
                        Toast.makeText(this, "Demande de collecte soumise avec succès", Toast.LENGTH_SHORT).show()
                        finish()
                    }
                }
            } else {
                Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun loadProductsIntoSpinner() {
        // Simuler un appel d'API ou charger depuis la base de données
        val products = listOf("Produit A", "Produit B", "Produit C")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, products)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productSpinner.adapter = adapter
    }

    private fun setupDatePicker() {
        val calendar = Calendar.getInstance()
        val year = calendar.get(Calendar.YEAR)
        val month = calendar.get(Calendar.MONTH)
        val day = calendar.get(Calendar.DAY_OF_MONTH)

        dateEditText.setOnClickListener {
            DatePickerDialog(this, { _, selectedYear, selectedMonth, selectedDay ->
                val selectedDate = "$selectedYear-${selectedMonth + 1}-$selectedDay"
                dateEditText.setText(selectedDate)
            }, year, month, day).show()
        }
    }
}
