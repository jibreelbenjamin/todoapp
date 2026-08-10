<?php

use App\models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/callback', function (Request $request) {
    $userId = $request->state;

    $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'code' => $request->code,
        'grant_type' => 'authorization_code',
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
    ]);

    $data = $response->json();
    // dd($data);

    if (empty($data['access_token'])) {
        return response()->json([
            'message' => 'Google Calendar gagal terhubung',
        ]);
    }

    // dd($data);
    User::find($userId)->update([
        'google_access_token' => $data['access_token'],
        'google_refresh_token' => $data['refresh_token'] ?? null,
    ]);

    return response()->json([
        // 'token' => $data['access_token'],
        // 'refresh_token' => $data['refresh_token'] ?? null,
        'message' => 'Google Calendar berhasil terhubung!',
    ]);
});
