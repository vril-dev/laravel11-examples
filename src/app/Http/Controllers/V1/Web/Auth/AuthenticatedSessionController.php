<?php

namespace App\Http\Controllers\V1\Web\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return Response
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword'  => Route::has('password.request'),
            'status'            => session('status'),
            'googleAuth'        => config('services.google.client_id')
                && config('services.google.client_secret'),
            'xAuth'             => config('services.twitter-oauth-2.client_id')
                && config('services.twitter-oauth-2.client_secret'),
            'twitchAuth'        => config('services.twitch.client_id')
                && config('services.twitch.client_secret'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param LoginRequest $request
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()
            ->regenerate();

        activity()
            ->info(__(':name has logged in.', [
                'name' => user('name'),
            ]));

        return redirect()
            ->intended(route(name: 'dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')
            ->logout();

        $request->session()
            ->invalidate();
        $request->session()
            ->regenerateToken();

        return redirect('/');
    }
}