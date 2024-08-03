package com.example.nomoreswaste

import android.content.Intent
import android.os.Bundle
import android.util.Log
import android.widget.Button
import android.widget.EditText
import android.widget.Toast
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.R
import com.example.nomorewaste.api.ApiService
import com.example.nomorewaste.api.LoginRequest
import com.example.nomorewaste.api.LoginResponse
import com.example.nomorewaste.api.RetrofitClient
import com.example.nomorewaste.espace.MerchantActivity
import com.example.nomorewaste.espace.VolunteerActivity
import retrofit2.Call
import retrofit2.Callback
import retrofit2.Response

class LoginActivity : AppCompatActivity() {
    private lateinit var emailEditText: EditText
    private lateinit var passwordEditText: EditText
    private lateinit var loginButton: Button
    private var apiService: ApiService? = null

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_login)

        emailEditText = findViewById(R.id.email)
        passwordEditText = findViewById(R.id.password)
        loginButton = findViewById(R.id.login_button)

        val retrofit = RetrofitClient.getClient()
        apiService = retrofit.create(ApiService::class.java)

        loginButton.setOnClickListener { login() }
    }

    private fun login() {
        val email = emailEditText.text.toString()
        val password = passwordEditText.text.toString()
        if (email.isEmpty() || password.isEmpty()) {
            Toast.makeText(this, "Email and password must not be empty", Toast.LENGTH_SHORT).show()
            return
        }
        val request = LoginRequest(email, password)
        apiService?.login(request)?.enqueue(object : Callback<LoginResponse?> {
            override fun onResponse(call: Call<LoginResponse?>, response: Response<LoginResponse?>) {
                if (response.isSuccessful) {
                    val loginResponse = response.body()
                    if (loginResponse != null) {
                        val userId = loginResponse.id
                        val role = loginResponse.role

                        // Log the user ID received from the server
                        Log.d("LoginActivity", "Login successful. Received User ID: $userId")

                        val sharedPreferences = getSharedPreferences("NoMoreWastePrefs", MODE_PRIVATE)
                        val editor = sharedPreferences.edit()
                        editor.putInt("USER_ID", userId)
                        editor.apply()

                        // Verify the stored ID
                        val storedUserId = sharedPreferences.getInt("USER_ID", -1)
                        Log.d("LoginActivity", "Stored User ID in SharedPreferences: $storedUserId")

                        when (role) {
                            "merchant" -> {
                                val intent = Intent(this@LoginActivity, MerchantActivity::class.java)
                                startActivity(intent)
                                finish()
                            }
                            "volunteer" -> {
                                val intent = Intent(this@LoginActivity, VolunteerActivity::class.java)
                                startActivity(intent)
                                finish()
                            }
                            else -> {
                                Toast.makeText(this@LoginActivity, "Role not supported", Toast.LENGTH_SHORT).show()
                            }
                        }
                    } else {
                        Toast.makeText(this@LoginActivity, "Login failed: Empty response", Toast.LENGTH_SHORT).show()
                    }
                } else {
                    Log.e("LoginActivity", "Login failed with response code: ${response.code()} and message: ${response.message()}")
                    Toast.makeText(this@LoginActivity, "Login failed: ${response.message()}", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<LoginResponse?>, t: Throwable) {
                Log.e("LoginActivity", "onFailure: ", t)
                Toast.makeText(this@LoginActivity, "An error occurred: ${t.message}", Toast.LENGTH_SHORT).show()
            }
        })
    }
}
