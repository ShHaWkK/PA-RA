package com.example.nomorewaste

import android.os.Bundle
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.api.ServiceProposalRequest
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class ServicePropositionActivity : AppCompatActivity() {

    private lateinit var apiService: ApiService
    private lateinit var proposeButton: Button
    private lateinit var nameEditText: EditText
    private lateinit var descriptionEditText: EditText

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_service_proposition)

        nameEditText = findViewById(R.id.editText_service_name)
        descriptionEditText = findViewById(R.id.editText_service_description)
        proposeButton = findViewById(R.id.button_propose_service)

        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
        val userId = sharedPreferences.getInt("USER_ID", -1)

        apiService = RetrofitClient.getClient().create(ApiService::class.java)

        proposeButton.setOnClickListener {
            if (userId != -1) {
                proposeService(userId)
            } else {
                Toast.makeText(this, "Erreur de récupération de l'ID de l'utilisateur", Toast.LENGTH_SHORT).show()
            }
        }
    }

    private fun proposeService(userId: Int) {
        val name = nameEditText.text.toString()
        val description = descriptionEditText.text.toString()

        if (name.isEmpty() || description.isEmpty()) {
            Toast.makeText(this, "Veuillez remplir tous les champs", Toast.LENGTH_SHORT).show()
            return
        }

        val proposal = ServiceProposalRequest(name, description, userId)
        apiService.proposeService(proposal).enqueue(object : Callback<Void> {
            override fun onResponse(call: Call<Void>, response: Response<Void>) {
                if (response.isSuccessful) {
                    Toast.makeText(this@ServicePropositionActivity, "Proposition envoyée avec succès", Toast.LENGTH_SHORT).show()
                    finish() // Close activity after successful submission
                } else {
                    Toast.makeText(this@ServicePropositionActivity, "Erreur lors de l'envoi de la proposition", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<Void>, t: Throwable) {
                Toast.makeText(this@ServicePropositionActivity, "Échec de la connexion : ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
