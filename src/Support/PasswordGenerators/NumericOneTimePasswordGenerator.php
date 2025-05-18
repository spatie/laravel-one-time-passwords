<?php

namespace Spatie\LaravelOneTimePasswords\Support\PasswordGenerators;

class NumericOneTimePasswordGenerator implements OneTimePasswordGenerator
{
    protected int $numberOfDigits = 6;

    public function __construct(){
        $this->numberOfDigits(config('one-time-passwords.password_length'));
    }

    public function numberOfDigits(int $numberOfDigits): self
    {
        $this->numberOfDigits = $numberOfDigits;

        return $this;
    }

    public function generate(): string
    {
        $min = 10 ** ($this->numberOfDigits - 1);
        $max = (10 ** $this->numberOfDigits) - 1;

        return (string) random_int($min, $max);
    }
}
