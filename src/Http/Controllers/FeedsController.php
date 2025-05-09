<?php

namespace MarJose123\Ninshiki\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class FeedsController
{
    public function index(Request $request)
    {
        $token = $request->session()->get('token');
        $response = Http::pool(fn (Pool $pool) => [
            $pool->as('posts')
                ->ninshiki()
                ->withToken($token)
                ->withQueryParameters([
                    'page' => $request->has('page') ? $request->get('page') : 1,
                    'per_page' => $request->has('per_page') ? $request->get('per_page') : 15,
                ])
                ->get(config('ninshiki.api_version').'/posts'),
            $pool->as('walletCredit')
                ->ninshiki()
                ->withToken($token)
                ->get(config('ninshiki.api_version').'/wallets/spend/balance'),
            $pool->as('walletEarned')
                ->ninshiki()
                ->withToken($token)
                ->get(config('ninshiki.api_version').'/wallets/default/balance'),
        ]);

        $posts = $response['posts']->json();
        $posts = [
            'data' => $posts['data'],
            'meta' => [
                'current_page' => $posts['meta']['current_page'],
                'last_page' => $posts['meta']['last_page'],
                'per_page' => $posts['meta']['per_page'],
                'total' => $posts['meta']['total'],
                'from' => $posts['meta']['from'],
                'to' => $posts['meta']['to'],
            ],
        ];

        if ($request->wantsJson() && ($request->has('page') || $request->has('per_page'))) {
            return response()->json($posts, $response['posts']->status());
        }

        return Inertia::render('feed/index', [
            'posts' => $posts,
            'wallet_credit' => $response['walletCredit']->json(),
            'wallet_earned' => $response['walletEarned']->json(),
        ]);
    }

    /**
     * @throws ConnectionException
     */
    public function likeUnlike(Request $request, $id)
    {
        $response = Http::ninshiki()
            ->withToken($request->session()->get('token'))
            ->patch(config('ninshiki.api_version').'/posts/'.$id.'/toggle/like');

        return response()->json($response->json(), $response->status());
    }

    /**
     * @throws ConnectionException
     */
    public function createPost(Request $request)
    {
        $response = Http::ninshiki()
            ->withToken($request->session()->get('token'))
            ->post(config('ninshiki.api_version').'/posts', [
                'post_content' => $request->post_content,
                'amount' => $request->points,
                ...($request->has('gif_url') ? [
                    'attachment_type' => $request->attachment_type,
                    'gif_url' => $request->gif_url,
                ] : []),
                'type' => 'user',
                'recipient_id' => $request->recipient_id,
            ]);

        if ($response->status() === 422) {
            throw ValidationException::withMessages($response->json()['error']['errors']);
        }
        if ($response->status() === 429) {
            return back()
                ->withErrors([
                    'tooManyRequest' => $response->json()['error']['message'],
                ]);
        }
        if ($response->status() !== 201) {
            return back()->withErrors([
                'error' => $response->json()['error']['message'],
            ]);
        }

        return to_route('feed');

    }

    /**
     * @param  Request  $request
     * @param  $id
     * @return JsonResponse|Response|\Symfony\Component\HttpFoundation\Response
     *
     * @throws ConnectionException
     */
    public function show(Request $request, $id)
    {
        $response = Http::ninshiki()
            ->withToken($request->session()->get('token'))
            ->get(config('ninshiki.api_version').'/posts/'.$id);

        if ($response->status() === 404) {
            return Inertia::render('error/index', [
                'status' => $response->getStatusCode(),
                'redirect' => route('feed'),
            ])
                ->toResponse($request)
                ->setStatusCode($response->status());
        }

        return Inertia::render('feed/view', [
            'post' => $response->json('data'),
        ]);

    }
}
