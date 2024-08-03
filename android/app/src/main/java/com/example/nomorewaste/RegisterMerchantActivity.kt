package com.example.nomorewaste

import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RegisterMerchantRequest
import com.example.nomorewaste.api.RetrofitClient
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response


class RegisterMerchantActivity : AppCompatActivity() {
    private lateinit var firstNameEditText: EditText
    private lateinit var lastNameEditText: EditText
    private lateinit var emailEditText: EditText
    private lateinit var phoneNumberEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var companyNameEditText: EditText
    private lateinit var siretEditText: EditText
    private lateinit var addressEditText: EditText
    private lateinit var registerButton: Button
    private var apiService: ApiService? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_register_merchant)

        firstNameEditText = findViewById(R.id.first_name)
        lastNameEditText = findViewById(R.id.last_name)
        emailEditText = findViewById(R.id.email)
        phoneNumberEditText = findViewById(R.id.phone_number)
        passwordEditText = findViewById(R.id.password)
        companyNameEditText = findViewById(R.id.company_name)
        siretEditText = findViewById(R.id.siret)
        addressEditText = findViewById(R.id.address)
        registerButton = findViewById(R.id.register_button)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        registerButton.setOnClickListener { register() }
    }

    private fun register() {
        val firstName = firstNameEditText.text.toString()
        val lastName = lastNameEditText.text.toString()
        val email = emailEditText.text.toString()
        val phoneNumber = phoneNumberEditText.text.toString()
        val password = passwordEditText.text.toString()
        val companyName = companyNameEditText.text.toString()
        val siret = siretEditText.text.toString()
        val address = addressEditText.text.toString()

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || phoneNumber.isEmpty() || password.isEmpty() || companyName.isEmpty() || siret.isEmpty() || address.isEmpty()) {
            Toast.makeText(this, "Tous les champs sont obligatoires", Toast.LENGTH_SHORT).show()
            return
        }

        val request = RegisterMerchantRequest(firstName, lastName, email, phoneNumber, password, companyName, siret, address)
        apiService?.registerMerchant(request)?.enqueue(object : Callback<Void?> {
            override fun onResponse(call: Call<Void?>, response: Response<Void?>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@RegisterMerchantActivity, "Inscription réussie. Votre compte est en attente de validation par les administrateurs.", Toast.LENGTH_LONG).show()
                    finish()
                } else {
                    Toast.makeText(this@RegisterMerchantActivity, "Échec de l'inscription", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void?>, t: Throwable) {
                Log.e("RegisterMerchantActivity", "onFailure: ", t)
                Toast.makeText(this@RegisterMerchantActivity, "Une erreur s'est produite", Toast.LENGTH_SHORT).show()
            }
        })
    }
}