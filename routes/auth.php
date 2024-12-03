<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;

Route::post("login", LoginController::class);
Route::post("logout", LogoutController::class);
Route::post("register", RegisterController::class);
Route::post("password/email",  [PasswordResetController::class, "sendResetLinkEmail"]);
Route::post("password/reset",  [PasswordResetController::class, "reset"])->middleware("signed")->name("password.reset");
