<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeAdmin extends Command
{
    protected $signature = 'dekifit:admin {email} {--name=Dejan Badzukov} {--password= : Leave empty to generate one}';

    protected $description = 'Create the coach (admin) account, or reset its password';

    public function handle(): int
    {
        $password = $this->option('password') ?: Str::password(16, symbols: false);

        $user = User::firstOrNew(['email' => $this->argument('email')]);
        $user->forceFill([
            'name' => $user->name ?: $this->option('name'),
            'password' => $password,
            'is_admin' => true,
            'must_change_password' => false,
            'email_verified_at' => $user->email_verified_at ?? now(),
        ])->save();

        $this->info("Admin ready: {$user->email}");

        if (! $this->option('password')) {
            $this->line("Password: {$password}");
        }

        return self::SUCCESS;
    }
}
