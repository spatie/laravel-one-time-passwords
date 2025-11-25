<?php

namespace Spatie\OneTimePasswords\Tests\TestSupport;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Spatie\OneTimePasswords\OneTimePasswordsServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('cache.default', 'array');

        // Disable rate limiting for tests
        config()->set('one-time-passwords.rate_limit_attempts.max_attempts_per_user', 999);

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Spatie\\OneTimePasswords\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            LivewireServiceProvider::class,
            OneTimePasswordsServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', Encrypter::generateKey(config('app.cipher')));

        Schema::dropAllTables();

        $migration = include __DIR__.'/../../database/migrations/create_one_time_passwords_table.php.stub';
        $migration->up();

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function updateConfig(string $key, mixed $value): void
    {
        config()->set($key, $value);

        (new OneTimePasswordsServiceProvider($this->app))->packageRegistered();
    }
}
