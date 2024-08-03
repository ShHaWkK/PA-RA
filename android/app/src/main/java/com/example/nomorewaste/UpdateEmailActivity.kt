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

class UpdateEmailActivity : AppCompatActivity() {

    private lateinit var editEmail: EditText
    private lateinit var buttonSaveEmail: Button
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_update_email)

        editEmail = findViewById(R.id.edit_email)
        buttonSaveEmail = findViewById(R.id.button_save_email)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        buttonSaveEmail.setOnClickListener {
            val email = editEmail.text.toString()

            if (email.isEmpty()) {
                Toast.makeText(this, "L'email est obligatoire", Toast.LENGTH_SHORT).show()
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
                                firstName = user.firstName,
                                lastName = user.lastName,
                                email = email,
                                phoneNumber = user.phoneNumber,
                                password = user.password
                            )
                            updateUser(updatedUser)
                        }
                    } else {
                        Toast.makeText(this@UpdateEmailActivity, "Erreur lors de la récupération des détails de l'utilisateur", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<User>, t: Throwable) {
                    Toast.makeText(this@UpdateEmailActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }

    private fun updateUser(user: User) {
        apiService.updateUser(user.id, user).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UpdateEmailActivity, "Email modifié avec succès", Toast.LENGTH_SHORT).show()
                    finish()
                } else {
                    Toast.makeText(this@UpdateEmailActivity, "Erreur lors de la modification de l'email", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@UpdateEmailActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
