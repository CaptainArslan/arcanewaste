@component('mail::message')
# Welcome to {{ config('app.name') }}! 🎉

Hi **{{ $name }}**,

Your account has been successfully created. You can now access your account using the following credentials:

@component('mail::panel')
**Email:** {{ $email }}  
**Temporary Password:** `{{ $password }}`
@endcomponent

> **Important:** This is a temporary password. For security reasons, please change your password immediately using the mobile app or API.

---

## Getting Started

1. Open your mobile app or API client.  
2. Use the temporary password provided above to authenticate.  
3. Update your password and complete your profile.

@if(isset($web_url))
@component('mail::button', ['url' => $web_url, 'color' => 'primary'])
Login to Your Account
@endcomponent
@endif

---

Need help? Our support team is ready to assist you. Simply reply to this email or contact us at **support@example.com**.

Thanks,<br>
**The {{ config('app.name') }} Team**
@endcomponent
