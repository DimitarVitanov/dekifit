<?php

namespace App\Http\Middleware;

use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => [
                'user' => $request->user()?->only('id', 'name', 'email', 'is_admin', 'must_change_password'),
            ],
            'flash' => fn () => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
                'credentials' => $request->session()->get('credentials'),
            ],
            // Sidebar badges for the coach.
            'adminCounts' => fn () => $request->user()?->is_admin ? [
                'orders' => Order::where('status', 'pending')->count(),
                'tickets' => Ticket::where('status', Ticket::OPEN)->count(),
            ] : null,
        ];
    }
}
