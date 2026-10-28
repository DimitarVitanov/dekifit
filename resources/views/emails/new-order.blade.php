<x-mail::message :preheader="$order->name.' wants the '.$order->package_name.' package (€'.$order->price.').'">
<p class="eyebrow">New order</p>

# {{ $order->package_name }} for {{ $order->name }}

A new subscription request has come in via the DekiFit website. The customer has been emailed the bank details.

<x-mail::table>
| Field | Value |
| :---- | :---- |
| Package | {{ $order->package_name }} |
| Price | €{{ $order->price }} |
| Name | {{ $order->name }} |
| Email | {{ $order->email }} |
| Phone | {{ $order->phone }} |
| Notes | {{ $order->notes ?: '—' }} |
| Payment description | DekiFit {{ $order->package_name }} – {{ $order->name }} |
</x-mail::table>

When the bank transfer arrives, open the admin area and click **Confirm payment & grant access**. The client gets their dashboard login by email. Reply to this email to write to the customer directly.

<x-mail::button :url="route('admin.orders')">
Open orders
</x-mail::button>
</x-mail::message>
