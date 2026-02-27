<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:admin {email? : The email of the admin} {--from-env : Use values from .env}';

    /**
     * The title and description of the console command.
     *
     * @var string
     */
    protected $description = 'Create a new admin user for the central application';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $useEnv = $this->option('from-env');

        if ($useEnv || (!$email && env('ADMIN_EMAIL'))) {
            $email = $email ?: env('ADMIN_EMAIL');
            $name = env('ADMIN_NAME', 'Administrator');
            $password = env('ADMIN_PASSWORD');

            if (!$password) {
                $this->error('ADMIN_PASSWORD is not set in .env');
                return 1;
            }

            $this->info("Using admin data from .env for {$email}");
        } else {
            if (!$email) {
                $email = $this->ask('Enter admin email', 'admin@example.com');
            }
            $name = $this->ask('Enter admin name', 'Administrator');
            $password = $this->secret('Enter admin password');

            if (!$password) {
                $password = 'password';
                $this->info('No password provided, using default: password');
            }
        }

        if (User::where('email', $email)->exists()) {
            $this->error("User with email {$email} already exists.");
            return 1;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => env('ADMIN_ROLE', 'admin'),
        ]);

        $this->info("Admin user created successfully for {$email}");

        return 0;
    }
}
