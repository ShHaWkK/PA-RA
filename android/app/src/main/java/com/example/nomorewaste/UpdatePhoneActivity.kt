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

class UpdatePhoneActivity : AppCompatActivity() {

    private lateinit var editPhone: EditText
    private lateinit var buttonSavePhone: Button
    private lateinit var apiService: ApiService

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_update_phone)

        editPhone = findViewById(R.id.edit_phone)
        buttonSavePhone = findViewById(R.id.button_save_phone)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        buttonSavePhone.setOnClickListener {
            val phone = editPhone.text.toString()

            if (phone.isEmpty()) {
                Toast.makeText(this, "Le téléphone est obligatoire", Toast.LENGTH_SHORT).show()
                return@setOnClickListener
            }

            val updatedUser = User(id = userId, firstName = "", lastName = "", email = "", phoneNumber = phone)
            apiService.updateUser(userId, updatedUser).enqueue(object : Callback<Void> {
                override fun onResponse(call: Call<Void>, response: Response<Void>) {
                    if (response.isSuccessful) {
                        Toast.makeText(this@UpdatePhoneActivity, "Téléphone modifié avec succès", Toast.LENGTH_SHORT).show()
                        finish()
                    } else {
                        Toast.makeText(this@UpdatePhoneActivity, "Erreur lors de la modification du téléphone", Toast.LENGTH_SHORT).show()
                    }
                }

                override fun onFailure(call: Call<Void>, t: Throwable) {
                    Toast.makeText(this@UpdatePhoneActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
                }
            })
        }
    }
}
