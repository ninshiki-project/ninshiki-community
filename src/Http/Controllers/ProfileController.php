<?php

namespace MarJose123\Ninshiki\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class ProfileController
{
    /**
     * @throws ConnectionException
     */
    public function index()
    {
        $resp = Http::ninshiki()
            ->withToken(\request()->session()->get('token'))
            ->get(config('ninshiki.api_version').'/sessions');

        return Inertia::render('profile/index', [
            'devices' => $resp->json(),
        ]);
    }
}
