<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\ClientAccessGranted;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Orders', [
            'orders' => Order::latest()->limit(200)->get(),
        ]);
    }

    /**
     * One click: mark the order paid, create (or reuse) the client account,
     * attach the package and email the login details.
     */
    public function grantAccess(Order $order)
    {
        if ($order->status === 'paid' && $order->user_id) {
            return back()->with('error', 'Access was already granted for this order.');
        }

        $existing = User::where('email', $order->email)->first();

        if ($existing?->is_admin) {
            return back()->with('error', 'This email belongs to an admin account.');
        }

        $password = null;

        $user = DB::transaction(function () use ($order, $existing, &$password) {
            $user = $existing;

            if (! $user) {
                $password = Str::password(12, symbols: false);
                $user = User::create([
                    'name' => $order->name,
                    'email' => $order->email,
                    'phone' => $order->phone,
                    'password' => $password,
                    'must_change_password' => true,
                ]);
            }

            $user->update(['program_id' => $order->program_id ?? $user->program_id]);
            $order->update(['status' => 'paid', 'user_id' => $user->id]);

            return $user;
        });

        $mailed = true;

        try {
            Mail::to($user->email)->send(new ClientAccessGranted($user, $order, $password));
        } catch (\Throwable $e) {
            report($e);
            $mailed = false;
        }

        // Shown to the coach once, so the login can be passed on by hand if the email fails.
        return back()->with('credentials', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $password,
            'mailed' => $mailed,
        ]);
    }

    public function cancel(Order $order)
    {
        if ($order->status !== 'pending') {
            return back()->with('error', 'Only pending orders can be cancelled.');
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('success', 'Order cancelled.');
    }
}
