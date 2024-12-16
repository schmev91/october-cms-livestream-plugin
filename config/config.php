<?php


return [
    // Token truy cập API của Alabapay
    "access_key"   => env("ALABAPAY_LIVESTREAM_KEY", null),

    // Url của Alabapay, phải kết thúc bằng dấu /
    // "alabapay_url" => "http://genius_alaba.test/",
    "alabapay_url" => "http://localhost:8002/",

    // Class name của đối tượng User
    "user_class"   => \Backend\Models\User::class,
 ];