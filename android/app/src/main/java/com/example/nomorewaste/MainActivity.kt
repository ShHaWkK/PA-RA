package com.example.nomoreswaste

import android.content.Intent
import android.os.Bundle
import android.widget.Button
import androidx.appcompat.app.AppCompatActivity
import com.example.nomorewaste.R
import com.example.nomorewaste.RegisterMerchantActivity
import com.example.nomorewaste.RegisterVolunteerActivity

class MainActivity : AppCompatActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_main)

        val loginButton: Button = findViewById(R.id.loginButton)
        val registerVolunteerButton: Button = findViewById(R.id.registerVolunteerButton)
        val registerMerchantButton: Button = findViewById(R.id.registerMerchantButton)

        loginButton.setOnClickListener {
            val intent = Intent(this, LoginActivity::class.java)
            startActivity(intent)
        }

        registerVolunteerButton.setOnClickListener {
            val intent = Intent(this, RegisterVolunteerActivity::class.java)
            startActivity(intent)
        }

        registerMerchantButton.setOnClickListener {
            val intent = Intent(this, RegisterMerchantActivity::class.java)
            startActivity(intent)
        }
    }
}
