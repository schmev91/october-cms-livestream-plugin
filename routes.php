<?php

use Pi\Livestream\Controllers\Livestreams;
use Pi\Livestream\Middlewares\CheckAccessKey;

Route::prefix('api/livestream')->middleware([ CheckAccessKey::class ])->group(function () {
    Route::get('/', [ Livestreams::class, 'activeLivestreamsGet' ]);
    Route::post('generate-token', [ Livestreams::class, 'tokenGenerate' ]);
    Route::post('end', [ Livestreams::class, 'livestreamEnd' ]);
});
