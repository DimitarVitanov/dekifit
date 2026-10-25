<x-mail::message>
# New Order — {{ $order->package_name }}

A new subscription request has come in via the DekiFit website.

<x-mail::table>
| Field | Value |
| :---- | :---- |
| Package | {{ $order->package_name }} |
| Price | €{{ $order->price }} |
| Name | {{ $order->name }} |
| Email | {{ $order->email }} |
| Phone | {{ $order->phone }} |
| Notes | {{ $order->notes ?: '—' }} |
</x-mail::table>

When the bank transfer arrives, open the admin area and click **Confirm payment & grant access** — the client gets their dashboard login by email.

<x-mail::button :url="route('admin.orders')">
Open orders
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
