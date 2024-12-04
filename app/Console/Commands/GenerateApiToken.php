<?php

namespace App\Console\Commands;

use App\Models\User; 
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateApiToken extends Command
{
    protected $signature = 'make:api-token {email}';
    protected $description = 'Generate API token for a user';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error('User not found!');
            return;
        }

        $token = $user->createToken('api-token')->plainTextToken;

        $this->info('Token generated successfully:');
        $this->info($token);
    }
}