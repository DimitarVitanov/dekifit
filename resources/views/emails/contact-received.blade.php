<x-mail::message preheader="Your message reached me. I'll reply within 24 hours.">
<p class="eyebrow">Message received</p>

# Thanks, {{ $firstName }}!

Your message reached me, and I'll get back to you personally within 24 hours, usually sooner.

Need an answer faster? Message me on WhatsApp or Viber at **{{ config('dekifit.whatsapp') }}**.

While you wait, have a look at the packages. Every one comes with a training program, a nutrition plan, progress tracking and direct support from me.

<x-mail::button :url="route('programs')">
See the packages
</x-mail::button>

<p class="signoff">Talk soon,<br>
Dejan Badzukov<br>
<span class="muted">Your DekiFit coach</span></p>

<x-slot:subcopy>
You're receiving this because someone sent a message with this email address through the contact form on dekifit.com. If that wasn't you, you can safely ignore this email.
</x-slot:subcopy>
</x-mail::message>
