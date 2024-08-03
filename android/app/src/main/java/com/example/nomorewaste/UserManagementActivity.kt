package com.example.nomorewaste

import android.content.Context
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.User
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class UserManagementActivity : AppCompatActivity() {

    private lateinit var editFirstName: EditText
    private lateinit var editLastName: EditText
    private lateinit var editEmail: EditText
    private lateinit var editPhone: EditText
    private lateinit var buttonSaveChanges: Button
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_user_management)

        editFirstName = findViewById(R.id.edit_first_name)
        editLastName = findViewById(R.id.edit_last_name)
        editEmail = findViewById(R.id.edit_email)
        editPhone = findViewById(R.id.edit_phone)
        buttonSaveChanges = findViewById(R.id.button_save_changes)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        // Récupérer l'ID de l'utilisateur depuis SharedPreferences
        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", Context.MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        if (userId != -1) {
            getUserDetails(userId)
        } else {
            Toast.makeText(this, "Erreur de récupération de l'ID de l'utilisateur", Toast.LENGTH_SHORT).show()
            finish()
        }

        buttonSaveChanges.setOnClickListener {
            saveUserChanges(userId)
        }
    }

    private fun getUserDetails(userId: Int) {
        apiService.getUser(userId).enqueue(object : Callback<User> {
            override fun onResponse(call: Call<User>, response: Response<User>) {
                if (response.isSuccessful) {
                    val user = response.body()
                    user?.let {
                        editFirstName.setText(it.firstName)
                        editLastName.setText(it.lastName)
                        editEmail.setText(it.email)
                        editPhone.setText(it.phoneNumber)
                    }
                } else {
                    Toast.makeText(this@UserManagementActivity, "Erreur de récupération des détails de l'utilisateur", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<User>, t: Throwable) {
                Toast.makeText(this@UserManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }

    private fun saveUserChanges(userId: Int) {
        val firstName = editFirstName.text.toString()
        val lastName = editLastName.text.toString()
        val email = editEmail.text.toString()
        val phone = editPhone.text.toString()

        if (firstName.isEmpty() || lastName.isEmpty() || email.isEmpty() || phone.isEmpty()) {
            Toast.makeText(this, "Tous les champs sont obligatoires", Toast.LENGTH_SHORT).show()
            return
        }

        val updatedUser = User(id = userId, firstName = firstName, lastName = lastName, email = email, phoneNumber = phone)

        apiService.updateUser(userId, updatedUser).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UserManagementActivity, "Modifications enregistrées avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@UserManagementActivity, "Erreur lors de l'enregistrement des modifications", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@UserManagementActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
