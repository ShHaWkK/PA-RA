package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.view.View
import android.widget.*
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.Product
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.Warehouse
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class AddProductActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var warehouseSpinner: Spinner
    private lateinit var warehouses: List<Warehouse>
    private var selectedWarehouseId: Int? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_add_product)

        // Initialisation des composants UI
        val nameEditText: EditText = findViewById(R.id.editTextProductName)
        val barcodeEditText: EditText = findViewById(R.id.editTextProductBarcode)
        val expirationDateEditText: EditText = findViewById(R.id.editTextExpirationDate)
        val volumeEditText: EditText = findViewById(R.id.editTextVolume)
        val saveButton: Button = findViewById(R.id.buttonSaveProduct)
        warehouseSpinner = findViewById(R.id.spinnerWarehouse)

        // Initialisation du service API
        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        // Chargement des entrepôts au démarrage de l'activité
        loadWarehouses()

        // Gestion du clic sur le bouton "Enregistrer"
        saveButton.setOnClickListener {
            val name = nameEditText.text.toString().trim()
            val barcode = barcodeEditText.text.toString().trim()
            val expirationDate = expirationDateEditText.text.toString().trim()
            val volume = volumeEditText.text.toString().trim().toFloatOrNull()

            if (validateInputs(name, barcode, expirationDate, volume)) {
                val product = Product(name, barcode, expirationDate, volume!!, selectedWarehouseId!!)
                Log.d("AddProductActivity", "Payload: $product")
                addProduct(product)
            }
        }
    }

    // Fonction pour valider les entrées de l'utilisateur
    private fun validateInputs(name: String, barcode: String, expirationDate: String, volume: Float?): Boolean {
        return if (name.isEmpty() || barcode.isEmpty() || expirationDate.isEmpty() || volume == null || selectedWarehouseId == null) {
            Toast.makeText(this, "Tous les champs sont requis", Toast.LENGTH_SHORT).show()
            false
        } else true
    }

    // Fonction pour charger les entrepôts depuis l'API
    private fun loadWarehouses() {
        apiService.getWarehouses().enqueue(object : Callback<List<Warehouse>> {
            override fun onResponse(call: Call<List<Warehouse>>, response: Response<List<Warehouse>>) {
                if (response.isSuccessful) {
                    warehouses = response.body() ?: emptyList()
                    setupWarehouseSpinner(warehouses)
                } else {
                    Toast.makeText(this@AddProductActivity, "Erreur lors du chargement des entrepôts", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<List<Warehouse>>, t: Throwable) {
                Toast.makeText(this@AddProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    // Fonction pour configurer le Spinner des entrepôts
    private fun setupWarehouseSpinner(warehouses: List<Warehouse>) {
        val warehouseNames = warehouses.map { it.name }
        val adapter = ArrayAdapter(this@AddProductActivity, android.R.layout.simple_spinner_item, warehouseNames)
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item)
        warehouseSpinner.adapter = adapter

        warehouseSpinner.onItemSelectedListener = object : AdapterView.OnItemSelectedListener {
            override fun onItemSelected(parent: AdapterView<*>, view: View?, position: Int, id: Long) {
                selectedWarehouseId = warehouses[position].id
            }

            override fun onNothingSelected(parent: AdapterView<*>) {
                selectedWarehouseId = null
            }
        }
    }

    // Fonction pour envoyer le produit à l'API
    private fun addProduct(product: Product) {
        apiService.addProduct(product).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@AddProductActivity, "Produit ajouté avec succès", Toast.LENGTH_SHORT).show()
                    finish() // Terminer l'activité après un ajout réussi
                } else {
                    Toast.makeText(this@AddProductActivity, "Erreur lors de l'ajout du produit", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@AddProductActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
