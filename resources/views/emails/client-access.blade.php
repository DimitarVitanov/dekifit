<x-mail::message :preheader="'Payment confirmed. Your '.$order->package_name.' coaching starts now.'">
<p class="eyebrow">Payment confirmed</p>

# Welcome to DekiFit, {{ $firstName }}

Your payment for the **{{ $order->package_name }}** package is confirmed and your client dashboard is ready.

@if ($password)
<x-mail::panel>
**Login email:** {{ $user->email }}<br>
**Password:** <span class="mono">{{ $password }}</span>
</x-mail::panel>

Please change this password after your first login (Account → Change password).
@else
Your **{{ $order->package_name }}** package is now active on your existing account. Log in with your current password, or [reset it here]({{ route('password.request') }}) if you've forgotten it.
@endif

<x-mail::button :url="$dashboardUrl">
Open my dashboard
</x-mail::button>

In your dashboard you can follow news and announcements, track your progress, keep notes, ask me questions and book your sessions.

<p class="signoff">Train. Focus. Transform.<br>
Dejan Badzukov<br>
<span class="muted">Your DekiFit coach</span></p>

@if ($verifyUrl)
<x-slot:subcopy>
The button also confirms your email address, so password resets and updates from your coach reach you. The link works for 7 days; after that, just log in from the [client login page]({{ route('login') }}).
</x-slot:subcopy>
@endif
</x-mail::message>
