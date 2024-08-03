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

            val updatedUser = User(id = userId, firstName = "", lastName = "", email = "", phoneNumber = "", password = password)
            apiService.updateUser(userId, updatedUser).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@UpdatePasswordActivity, "Mot de passe modifié avec succès", Toast.LENGTH_SHORT).show()
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
}
