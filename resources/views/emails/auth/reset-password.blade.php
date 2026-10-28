<x-mail::message :preheader="'Choose a new password for your DekiFit account. The link expires in '.$expires.' minutes.'">
<p class="eyebrow">Password reset</p>

# Reset your password

Hi {{ $firstName }}, we received a request to reset the password for your DekiFit account (**{{ $user->email }}**). Tap the button below to choose a new one.

<x-mail::button :url="$url">
Choose a new password
</x-mail::button>

This link expires in **{{ $expires }} minutes**. If you didn't ask for a reset, you can ignore this email and your password stays the same.

<p class="signoff">Train. Focus. Transform.<br>
DekiFit</p>

<x-slot:subcopy>
Button not working? Copy this link into your browser:<br>
[{{ $url }}]({{ $url }})
</x-slot:subcopy>
</x-mail::message>
