<x-mail::message>
# Welcome to DekiFit, {{ $user->name }}

Your payment for the **{{ $order->package_name }}** package is confirmed and your client dashboard is ready.

@if ($password)
<x-mail::panel>
**Username:** {{ $user->email }}<br>
**Password:** {{ $password }}
</x-mail::panel>

Please change this password after your first login (Account → Change password).
@else
Your **{{ $order->package_name }}** package is now active on your existing account — log in with your current password.
@endif

<x-mail::button :url="route('login')">
Open my dashboard
</x-mail::button>

In your dashboard you can follow news and announcements, track your progress, keep notes, ask me questions and book your sessions.

Train. Focus. Transform.<br>
Dejan — {{ config('app.name') }}
</x-mail::message>
