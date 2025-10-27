<?php

use Illuminate\Support\Facades\Route;



$route_1 = "aW5zdGFsbA==";
$route_1 = base64_decode($route_1);

$route_2 = "aW5zdGFsbC92ZXJpZnk=";
$route_2 = base64_decode($route_2);


$route_3 = "aW5zdGFsbC9taWdyYXRl";
$route_3 = base64_decode($route_3);



Route::resource($route_1,    Moshabytes\Redis\Controllers\InstallerController::class)->middleware(['web']);
Route::post($route_2, [Moshabytes\Redis\Controllers\InstallerController::class, 'verify'])->name('install.verify')->middleware(['web']);
Route::post($route_3, [Moshabytes\Redis\Controllers\InstallerController::class, 'migrate'])->name('install.migrate')->middleware(['web']);
