<?php

use Inertia\Inertia;
use MarJose123\Ninshiki\Http\Controllers\AuthenticationController;
use MarJose123\Ninshiki\Http\Controllers\BroadcastController;
use MarJose123\Ninshiki\Http\Controllers\EmployeesController;
use MarJose123\Ninshiki\Http\Controllers\FeedsController;
use MarJose123\Ninshiki\Http\Controllers\GiftController;
use MarJose123\Ninshiki\Http\Controllers\GiphyController;
use MarJose123\Ninshiki\Http\Controllers\ProfileController;
use MarJose123\Ninshiki\Http\Middleware\EnsureAuthenticatedMiddleware;
use MarJose123\Ninshiki\Ninshiki;

Route::middleware(config('ninshiki.middleware'))
    ->domain(config('ninshiki.domain'))
    ->group(function () {

        /**
         * ------------------------------------------------------------------------------
         *  UNAUTHENTICATED ROUTE
         * ------------------------------------------------------------------------------
         */
        Route::middleware(config('ninshiki.guestMiddleware'))
            ->group(function () {
                Route::get('/', [AuthenticationController::class, 'index'])->name('login.page');
                Route::get('provider/zoho', [AuthenticationController::class, 'requestForProviderLogin'])->name('login.requestProvider');
                Route::get('callback/provider/zoho', [AuthenticationController::class, 'callbackForProviderLogin'])->name('login.callback');
            });

        /**
         * ------------------------------------------------------------------------------
         *  AUTHENTICATED ROUTE
         * ------------------------------------------------------------------------------
         */
        Route::middleware(array_merge(config('ninshiki.authMiddleware'), [EnsureAuthenticatedMiddleware::class]))
            ->group(function () {
                // logout
                Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
                // post
                Route::get('feed', [FeedsController::class, 'index'])->name('feed');
                Route::get('feed/{id}', [FeedsController::class, 'show'])->name('feed.show');
                Route::patch('feed/{id}', [FeedsController::class, 'likeUnlike'])->name('feeds.like-unlike');
                Route::post('feed', [FeedsController::class, 'createPost'])->name('feeds.create-post');
                // users
                Route::get('employees/list', [EmployeesController::class, 'index'])->name('employees.list');
                Route::get('employees', [EmployeesController::class, 'getAllEmployees'])->name('employees');
                // giphy
                Route::get('gif', [GiphyController::class, 'trending'])->name('gif.trending');
                Route::get('gif/search', [GiphyController::class, 'search'])->name('gif.search');

                // broadcast
                Route::post('broadcast/auth', BroadcastController::class)
                    ->name('broadcast.auth');

                // gift
                Route::post('gift/send', [GiftController::class, 'send'])->name('gift.send');

                // profile
                Route::get('profile', [ProfileController::class, 'index'])->name('profile');

            });
    });

Route::fallback(function () {
    Inertia::setRootView(Ninshiki::$rootViewApp);

    return Inertia::render('error/index', [
        'status' => 404,
    ]);
});
