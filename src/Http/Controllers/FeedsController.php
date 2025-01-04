<?php

namespace MarJose123\Ninshiki\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class FeedsController
{
    /**
     * @throws ConnectionException
     */
    public function index(Request $request)
    {
        $response = Http::ninshiki()
            ->withToken($request->session()->get('token'))
            ->withQueryParameters([
                'page' => $request->has('page') ? $request->get('page') : 1,
                'per_page' => $request->has('per_page') ? $request->get('per_page') : 15,
            ])
            ->get(config('ninshiki.api_version').'/posts');
        $posts = $response->json();
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
            return response()->json($posts, $response->status());
        }

        return Inertia::render('feed/index', [
            'posts' => $posts,
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

        return response()->json($response->json(), $response->status());

    }
}
