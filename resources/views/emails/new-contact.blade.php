<x-mail::message :preheader="$contact->name.' wrote via the contact form: '.$contact->subject_label.'.'">
<p class="eyebrow">Contact form</p>

# New message from {{ $contact->name }}

<x-mail::table>
| Field | Value |
| :---- | :---- |
| Name | {{ $contact->name }} |
| Email | {{ $contact->email }} |
| Phone | {{ $contact->phone ?: '—' }} |
| Topic | {{ $contact->subject_label }} |
</x-mail::table>

<x-mail::panel>
{{ preg_replace('/\R/', "  \n", trim($contact->message)) }}
</x-mail::panel>

They've been sent an automatic "thanks, I'll reply within 24 hours" email. **Just hit reply** to answer them directly.

<x-mail::button :url="'mailto:'.$contact->email">
Reply to {{ $contact->name }}
</x-mail::button>
</x-mail::message>
