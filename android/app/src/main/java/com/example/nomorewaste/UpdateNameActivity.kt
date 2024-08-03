package com.example.nomorewaste

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

class UpdateNameActivity : AppCompatActivity() {

    private lateinit var editFirstName: EditText
    private lateinit var editLastName: EditText
    private lateinit var buttonSaveName: Button
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_update_name)

        editFirstName = findViewById(R.id.edit_first_name)
        editLastName = findViewById(R.id.edit_last_name)
        buttonSaveName = findViewById(R.id.button_save_name)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        if (userId == -1) {
            Toast.makeText(this, "User ID not found in SharedPreferences", Toast.LENGTH_SHORT).show()
            finish()
            return
        }

        buttonSaveName.setOnClickListener {
            val firstName = editFirstName.text.toString()
            val lastName = editLastName.text.toString()

            if (firstName.isEmpty() || lastName.isEmpty()) {
                Toast.makeText(this, "Tous les champs sont obligatoires", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            // Retrieve the existing user details
            apiService.getUser(userId).enqueue(object : Callback<User> {
                override fun onResponse(call: Call<User>, response: Response<User>) {
                    if (response.isSuccessful) {
                        val user = response.body()
                        if (user != null) {
                            val updatedUser = User(
                                id = userId,
                                firstName = firstName,
                                lastName = lastName,
                                email = user.email,
                                phoneNumber = user.phoneNumber,
                                password = user.password
                            )
                            updateUser(updatedUser)
                        } else {
                            Toast.makeText(this@UpdateNameActivity, "User not found", Toast.LENGTH_SHORT).show()
                        }
                    } else {
                        Toast.makeText(this@UpdateNameActivity, "Erreur lors de la récupération des détails de l'utilisateur", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<User>, t: Throwable) {
                    Toast.makeText(this@UpdateNameActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }

    private fun updateUser(user: User) {
        apiService.updateUser(user.id, user).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UpdateNameActivity, "Nom modifié avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@UpdateNameActivity, "Erreur lors de la modification du nom", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@UpdateNameActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
