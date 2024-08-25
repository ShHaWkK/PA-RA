package com.example.nomorewaste

import android.app.DatePickerDialog
import android.os.Bundle
import android.widget.*
import androidx.activity.viewModels
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.viewmodel.CollectionRequestViewModel
import java.util.*

class RequestCollectionActivity : AppCompatActivity() {

    private val collectionRequestViewModel: CollectionRequestViewModel by viewModels()
    private lateinit var productSpinner: Spinner
    private lateinit var quantityEditText: EditText
    private lateinit var addressEditText: EditText
    private lateinit var dateEditText: EditText
    private lateinit var submitButton: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_request_collection)

        productSpinner = findViewById(R.id.spinner_product)
        quantityEditText = findViewById(R.id.edit_text_quantity)
        addressEditText = findViewById(R.id.edit_text_address)
        dateEditText = findViewById(R.id.edit_text_date)
        submitButton = findViewById(R.id.button_submit_request)

        setupProductSpinner()

        dateEditText.setOnClickListener {
            showDatePicker()
        }

        submitButton.setOnClickListener {
            val productId = productSpinner.selectedItemId.toInt()
            val quantity = quantityEditText.text.toString().toIntOrNull()
            val address = addressEditText.text.toString()
            val date = dateEditText.text.toString()

            if (quantity != null && address.isNotBlank() && date.isNotBlank()) {
                val requestData = mapOf(
                    "product_id" to productId,
                    "notified_quantity" to quantity,
                    "address" to address,
                    "wished_collection_date" to date
                )
                collectionRequestViewModel.createCollectionRequest(requestData)
            } else {
                Toast.makeText(this, "Please fill all fields correctly", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun showDatePicker() {
        val calendar = Calendar.getInstance()
        val datePickerDialog = DatePickerDialog(
            this,
            { _, year, month, dayOfMonth ->
                val date = "$year-${month + 1}-$dayOfMonth"
                dateEditText.setText(date)
            },
            calendar.get(Calendar.YEAR),
            calendar.get(Calendar.MONTH),
            calendar.get(Calendar.DAY_OF_MONTH)
        )
        datePickerDialog.show()
    }

    private fun setupProductSpinner() {
        // Replacez par les données de l'API réelle
        val products = listOf("Product A", "Product B", "Product C")
        val adapter = ArrayAdapter(this, android.R.layout.simple_spinner_item, products)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        productSpinner.adapter = adapter
    }
}
