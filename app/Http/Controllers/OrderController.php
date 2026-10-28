<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Mail\OrderReceived;
use App\Models\Order;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

use function Illuminate\Support\defer;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        $packages = Program::published()->get();
        $selectedSlug = $request->query('package');

        return Inertia::render('Checkout', [
            'packages' => $packages,
            'selectedSlug' => $selectedSlug,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'nullable|exists:programs,id',
            'package_name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'notes' => 'nullable|string|max:2000',
        ]);

        // Never trust the browser for the price of a known package.
        if ($program = Program::published()->find($validated['program_id'] ?? null)) {
            $validated['package_name'] = $program->title;
            $validated['price'] = $program->price;
        }

        $order = Order::create($validated);

        // Sent after the response, so checkout isn't kept waiting on the mail server.
        defer(function () use ($order) {
            rescue(fn () => Mail::to(config('dekifit.admin_email'))->send(new NewOrderNotification($order)));
            rescue(fn () => Mail::to($order->email, $order->name)->send(new OrderReceived($order)));
        });

        return Inertia::render('CheckoutThankYou', [
            'order' => $order,
        ]);
    }
}
