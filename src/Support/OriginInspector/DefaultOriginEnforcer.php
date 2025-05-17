<?php

namespace Spatie\LaravelOneTimePasswords\Support\OriginInspector;

use Illuminate\Http\Request;
use Spatie\LaravelOneTimePasswords\Models\OneTimePassword;

class DefaultOriginEnforcer implements OriginEnforcer
{
    public function gatherProperties(Request $request): array
    {
        return [
            'ip' => $request->ip(),
            'userAgent' => $request->userAgent(),
        ];
    }

    public function verifyProperties(OneTimePassword $oneTimePassword, Request $request): bool
    {
        $requestProperties = (array) ($oneTimePassword->origin_properties ?? []);

        if (!isset($requestProperties['ip']) || $requestProperties['ip'] !== $request->ip()) {
            return false;
        }

        if (!isset($requestProperties['userAgent']) || $requestProperties['userAgent'] !== $request->userAgent()) {
            return false;
        }

        return true;
    }
}
