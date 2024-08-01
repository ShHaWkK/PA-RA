// Path: com.example.nomoreswaste/LoginActivity.java
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

        val retrofit = RetrofitClient.getClient("http://10.0.2.2/")
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
            override fun onResponse(
                call: Call<LoginResponse?>,
                response: Response<LoginResponse?>
            ) {
                if (response.isSuccessful) {
                    val loginResponse = response.body()
                    if (loginResponse != null) {
                        val token = loginResponse.token
                        val role = loginResponse.role
                        when (role) {
                            "merchant" -> {
                                val intent = Intent(this@LoginActivity, MerchantActivity::class.java)
                                startActivity(intent)
                            }
                            "volunteer" -> {
                                val intent = Intent(this@LoginActivity, VolunteerActivity::class.java)
                                startActivity(intent)
                            }
                            else -> {
                                Toast.makeText(this@LoginActivity, "Role not supported", Toast.LENGTH_SHORT).show()
                            }
                        }
                    }
                } else {
                    Log.e("LoginActivity", "Login failed with response code: ${response.code()} and message: ${response.message()}")
                    Toast.makeText(this@LoginActivity, "Login failed", Toast.LENGTH_SHORT).show()
                }
            }

            override fun onFailure(call: Call<LoginResponse?>, t: Throwable) {
                Log.e("LoginActivity", "onFailure: ", t)
                Toast.makeText(this@LoginActivity, "An error occurred", Toast.LENGTH_SHORT).show()
            }
        })

    }
}