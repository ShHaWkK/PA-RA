package com.example.nomorewaste

import android.content.Intent
import android.os.Bundle
import androidx.appcompat.app.AppCompatActivity
import android.widget.Button

class UserManagementActivity : AppCompatActivity() {

    private lateinit var buttonUpdateName: Button
    private lateinit var buttonUpdateEmail: Button
    private lateinit var buttonUpdatePhone: Button
    private lateinit var buttonUpdatePassword: Button

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        setContentView(R.layout.activity_user_management)

        buttonUpdateName = findViewById(R.id.button_update_name)
        buttonUpdateEmail = findViewById(R.id.button_update_email)
        buttonUpdatePhone = findViewById(R.id.button_update_phone)
        buttonUpdatePassword = findViewById(R.id.button_update_password)

        buttonUpdateName.setOnClickListener {
            val intent = Intent(this, UpdateNameActivity::class.java)
            startActivity(intent)
        }

        buttonUpdateEmail.setOnClickListener {
            val intent = Intent(this, UpdateEmailActivity::class.java)
            startActivity(intent)
        }

        buttonUpdatePhone.setOnClickListener {
            val intent = Intent(this, UpdatePhoneActivity::class.java)
            startActivity(intent)
        }

        buttonUpdatePassword.setOnClickListener {
            val intent = Intent(this, UpdatePasswordActivity::class.java)
            startActivity(intent)
        }
    }
}
