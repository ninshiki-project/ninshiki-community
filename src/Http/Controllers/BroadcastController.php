<?php

namespace MarJose123\Ninshiki\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class BroadcastController
{
    /**
     * @throws ConnectionException
     */
    public function __invoke(Request $request)
    {
        $response = Http::ninshiki()
            ->withToken($request->session()->get('token'))
            ->post('/broadcasting/auth', [
                ...$request->all(),
            ]);

        return response()->json($response->json(), $response->status());
    }
}
