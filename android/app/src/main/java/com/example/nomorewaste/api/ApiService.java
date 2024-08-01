package com.example.nomorewaste.api;

import retrofit2.Call;
import retrofit2.http.Body;
import retrofit2.http.POST;

public interface ApiService {
@POST("login")
    Call<LoginResponse> login(@Body LoginRequest request);

@POST("registerVolunteer")
    Call<Void> registerVolunteer(@Body RegisterVolunteerRequest request);

@POST("registerMerchant")
    Call<Void> registerMerchant(@Body RegisterMerchantRequest request);

}