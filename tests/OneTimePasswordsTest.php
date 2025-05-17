<?php

use Spatie\LaravelOneTimePasswords\Enums\ConsumeOneTimePasswordResult;
use Spatie\LaravelOneTimePasswords\Rules\OneTimePasswordRule;
use Spatie\LaravelOneTimePasswords\Support\OriginInspector\DoNotEnforceOrigin;
use Spatie\LaravelOneTimePasswords\Tests\TestSupport\Models\User;

beforeEach(function () {
    /** @var $user User */
    $user = User::factory()->create();

    $this->user = $user;
});

it('can create a one-time password', function () {
    $this->user->createOneTimePassword();

    expect($this->user->oneTimePasswords)->toHaveCount(1);

    $oneTimePassword = $this->user->oneTimePasswords->first();

    expect($oneTimePassword->password)->toHaveLength(config('one-time-passwords.password_length'));
    expect($oneTimePassword->origin_properties)->toHaveKeys(['ip', 'userAgent']);

    $expectedExpiresAt = now()->addMinutes(config('one-time-passwords.default_expires_in_minutes'))->toDateTimeString();
    expect($oneTimePassword->expires_at->toDateTimeString())->toBe($expectedExpiresAt);
});

it('can consume a one-time password', function () {
    $oneTimePassword = $this->user->createOneTimePassword();

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);

    expect($result)->toBe(ConsumeOneTimePasswordResult::Ok);
});

it('will not work for another user', function () {
    $oneTimePassword = $this->user->createOneTimePassword();

    $anotherUser = User::factory()->create();

    $result = $anotherUser->consumeOneTimePassword($oneTimePassword->password);

    expect($result)->toBe(ConsumeOneTimePasswordResult::NoOneTimePasswordsFound);
});

it('will not return ok for a wrong password', function () {
    $this->user->createOneTimePassword();

    $result = $this->user->consumeOneTimePassword('wrong-password');

    expect($result)->toBe(ConsumeOneTimePasswordResult::IncorrectOneTimePassword);
});

it('will not return ok for a expired password', function (int $numberOfMinutes, ConsumeOneTimePasswordResult $expectedResult) {
    $oneTimePassword = $this->user->createOneTimePassword();

    $this->travel($numberOfMinutes)->minutes();

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);

    expect($result)->toBe($expectedResult);
})->with([
    [0, ConsumeOneTimePasswordResult::Ok],
    [1, ConsumeOneTimePasswordResult::Ok],
    [2, ConsumeOneTimePasswordResult::OneTimePasswordExpired],
]);

it('will not work again if it has already been consumed', function () {
    $oneTimePassword = $this->user->createOneTimePassword();
    expect($this->user->oneTimePasswords)->toHaveCount(1);

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);
    expect($result)->toBe(ConsumeOneTimePasswordResult::Ok);
    expect($this->user->refresh()->oneTimePasswords)->toHaveCount(0);

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);
    expect($result)->toBe(ConsumeOneTimePasswordResult::NoOneTimePasswordsFound);
});

it('will not accept old one-time passwords anymore when a new one is created', function () {
    $oldOneTimePassword = $this->user->createOneTimePassword();
    $newOneTimePassword = $this->user->createOneTimePassword();

    $resultForOldOneTimePassword = $this->user->consumeOneTimePassword($oldOneTimePassword->password);

    $resultForNewOneTimePassword = $this->user->consumeOneTimePassword($newOneTimePassword->password);

    expect($resultForOldOneTimePassword)->toBe(ConsumeOneTimePasswordResult::IncorrectOneTimePassword);
    expect($resultForNewOneTimePassword)->toBe(ConsumeOneTimePasswordResult::Ok);

});

it('will enforce the origin by default', function () {
    $oneTimePassword = $this->user->createOneTimePassword();

    $oneTimePassword->update([
        'origin_properties' => array_merge($oneTimePassword->origin_properties, ['userAgent' => 'some-other-user-agent']),
    ]);

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);
    expect($result)->toBe(ConsumeOneTimePasswordResult::DifferentOrigin);
});

it('has an inspector that does not enforce origin', function () {
    updateConfig('one-time-passwords.origin_enforcer', DoNotEnforceOrigin::class);

    $oneTimePassword = $this->user->createOneTimePassword();

    $oneTimePassword->update([
        'origin_properties' => array_merge($oneTimePassword->origin_properties, ['userAgent' => 'some-other-user-agent']),
    ]);

    $result = $this->user->consumeOneTimePassword($oneTimePassword->password);
    expect($result)->toBe(ConsumeOneTimePasswordResult::Ok);
});

it('can log in the user', function () {
    $oneTimePassword = $this->user->createOneTimePassword();

    expect(auth()->check())->toBeFalse();
    $result = $this->user->attemptLoginUsingOneTimePassword($oneTimePassword->password);
    expect($result)->toBe(ConsumeOneTimePasswordResult::Ok);
    expect(auth()->check())->toBeTrue();
});

it('will not login the user when the password is incorrect', function () {
    $oneTimePassword = $this->user->createOneTimePassword();

    expect(auth()->check())->toBeFalse();
    $result = $this->user->attemptLoginUsingOneTimePassword('wrong-password');
    expect($result)->toBe(ConsumeOneTimePasswordResult::IncorrectOneTimePassword);
    expect(auth()->check())->toBeFalse();
});

it('rejects non-string OTP codes', function () {
    $rule = new OneTimePasswordRule($this->user);
    
    $rule->validate('otp', 12345, function ($message) {
        expect($message)->toBe('The one-time password must be a string.');
    });
});