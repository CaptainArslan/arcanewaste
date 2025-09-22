@component('mail::message')
# 🔐 Your One-Time Password (OTP)

Hello **{{ $name }}**,

We received a request to verify your account.  
Please use the following **One-Time Password (OTP)** to complete the process:

@component('mail::panel')
## ✅ Your OTP Code:  
# **{{ $otp }}**
@endcomponent

⚠️ **Note:**  
This code will expire in **{{ $expiryMinutes }} minutes**.  
Do not share this code with anyone. Our team will never ask you for your OTP.

If you did not request this, you can safely ignore this email.

---

Thanks,  
**{{ config('app.name') }} Team**  
@endcomponent
