<?php

use App\Services\Scanner\CandidateRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Hello from Laravel API'
    ]);
});

Route::get('/scanner/candidates', function (CandidateRepository $repo) {
    return $repo->all();
});
