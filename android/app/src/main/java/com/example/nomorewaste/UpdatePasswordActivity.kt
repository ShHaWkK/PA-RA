package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.User
import com.example.nomorewaste.espace.VolunteerActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class UpdatePasswordActivity : AppCompatActivity() {

    private lateinit var editPassword: EditText
    private lateinit var buttonSavePassword: Button
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_update_password)

        editPassword = findViewById(R.id.edit_password)
        buttonSavePassword = findViewById(R.id.button_save_password)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        buttonSavePassword.setOnClickListener {
            val password = editPassword.text.toString()

            if (password.isEmpty()) {
                Toast.makeText(this, "Le mot de passe est obligatoire", Toast.LENGTH_SHORT).show()
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
                                email = user.email,
                                phoneNumber = user.phoneNumber,
                                password = password
                            )
                            updateUser(updatedUser)
                        }
                    } else {
                        Toast.makeText(this@UpdatePasswordActivity, "Erreur lors de la récupération des détails de l'utilisateur", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<User>, t: Throwable) {
                    Toast.makeText(this@UpdatePasswordActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }

    private fun updateUser(user: User) {
        apiService.updateUser(user.id, user).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@UpdatePasswordActivity, "Mot de passe modifié avec succès", Toast.LENGTH_SHORT).show()
                    // Return to VolunteerActivity
                    val intent = Intent(this@UpdatePasswordActivity, VolunteerActivity::class.java)
                    intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP or Intent.FLAG_ACTIVITY_NEW_TASK)
                    startActivity(intent)
                    finish()
                } else {
                    Toast.makeText(this@UpdatePasswordActivity, "Erreur lors de la modification du mot de passe", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@UpdatePasswordActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
