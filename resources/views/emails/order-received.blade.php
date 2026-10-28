<x-mail::message :preheader="'One step left: pay €'.$price.' by bank transfer and your coaching dashboard unlocks.'">
<p class="eyebrow">Request received</p>

# Thank you, {{ $firstName }}!

Your request for the **{{ $order->package_name }}** package is in. There's one step left: complete the bank transfer below, and as soon as your payment arrives I'll unlock your client dashboard.

<x-mail::table>
| Package | Order | Price |
| :------ | :---- | ----: |
| **{{ $order->package_name }}** | #{{ $order->id }} | **€{{ $price }}** |
</x-mail::table>

## Bank transfer details

<x-mail::panel>
**Amount:** €{{ $price }} EUR<br>
**Beneficiary:** {{ $bank['beneficiary'] }}<br>
**IBAN:** <span class="mono">{{ $iban }}</span><br>
**BIC / SWIFT:** <span class="mono">{{ $bank['bic'] }}</span><br>
**Beneficiary address:** {{ $bank['address'] }}<br>
**Bank:** {{ $bank['bank_name'] }}, {{ $bank['bank_address'] }}<br>
**Payment description:** {{ $reference }}
</x-mail::panel>

Please pay in euros (EUR) and copy the payment description exactly, so I can match your transfer to your order.

## What happens next

1. **Make the transfer** from any bank, using the details above. International transfers usually take 1–3 working days to arrive.
2. **I confirm your payment** as soon as it lands in my account.
3. **You get your login** by email: your training program, nutrition, progress tracking and a direct line to me, all in your client dashboard.

<x-mail::button :url="config('dekifit.whatsapp_url')">
Questions? Message me on WhatsApp
</x-mail::button>

<p class="signoff">Let's get to work,<br>
Dejan Badzukov<br>
<span class="muted">Your DekiFit coach</span></p>

<x-slot:subcopy>
You're receiving this because you ordered the {{ $order->package_name }} package on dekifit.com. If that wasn't you, just ignore this email. Nothing is charged until you make the transfer yourself.
</x-slot:subcopy>
</x-mail::message>
