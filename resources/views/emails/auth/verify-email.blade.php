<x-mail::message preheader="One tap to confirm your email address for your DekiFit dashboard.">
<p class="eyebrow">One quick step</p>

# Confirm your email

Hi {{ $firstName }}, please confirm that **{{ $user->email }}** is your email address. This makes sure password resets and updates from your coach always reach you.

<x-mail::button :url="$url">
Confirm my email
</x-mail::button>

This link expires in {{ $expires }} minutes. If you didn't request it, you can ignore this email.

<p class="signoff">Train. Focus. Transform.<br>
DekiFit</p>

<x-slot:subcopy>
Button not working? Copy this link into your browser:<br>
[{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>
