<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    if (!$request->hasValidSignature()) {
        abort(403, 'Invalid or expired verification link.');
    }

    $user = \App\Models\User::findOrFail($id);

    if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
        abort(403, 'Invalid verification hash.');
    }

    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }

    return redirect(config('app.frontend_url') . '/verify-email/success');
})->middleware('signed')->name('verification.verify');
