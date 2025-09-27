@component('mail::message')
# Welcome to {{ config('app.name') }}! 🎉

Hi **{{ $customer->full_name }}**,

Your account has been successfully created. You can now access your account using the following credentials:

@component('mail::panel')
**Email:** {{ $customer->email }}  
`**Temporary Password:** `{{ $password }}`  
@endcomponent

> **Important:** This is a temporary password. For security reasons, please log in and change your password immediately after your first login.

---

## Getting Started

1. Click the button below to log in.  
2. Use the temporary password provided above.  
3. Update your password and complete your profile.

@component('mail::button', ['url' => route('login'), 'color' => 'primary'])
Login to Your Account
@endcomponent

---

Need help? Our support team is ready to assist you. Simply reply to this email or contact us at **support@example.com**.

Thanks,<br>
**The {{ config('app.name') }} Team**
@endcomponent
