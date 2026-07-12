<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = text('Name', required: true);

        $email = text(
            'Email',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['email' => $value],
                ['email' => ['required', 'email', 'unique:admins,email']]
            )->errors()->first('email'),
        );

        $password = password(
            'Password',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['password' => $value],
                ['password' => ['required', 'string', 'min:12']]
            )->errors()->first('password'),
        );

        Admin::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info("Admin \"{$name}\" <{$email}> created.");

        return self::SUCCESS;
    }
}
