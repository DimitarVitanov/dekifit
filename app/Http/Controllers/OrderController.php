<?php

namespace App\Http\Controllers;

use App\Mail\NewOrderNotification;
use App\Models\Order;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

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

        $adminEmail = env('ADMIN_EMAIL', 'badzukov.d@hotmail.com');

        try {
            Mail::to($adminEmail)->send(new NewOrderNotification($order));
        } catch (\Throwable $e) {
            report($e);
        }

        return Inertia::render('CheckoutThankYou', [
            'order' => $order,
        ]);
    }
}
