<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\StudentController;
use Illuminate\Support\Facades\Route;

//AUTH
Route::post('/auths/login', [AuthController::class, "login"]);
Route::post('/auths/register', [AuthController::class, "register"]);
Route::middleware('auth:sanctum')->group(function () {
    Route::put('/auths/update/{id}', [AuthController::class, 'updateUser']);
    Route::post('/auths/logout', [AuthController::class, 'logoutUser']); // Ruta para cerrar sesión
    Route::delete('/auths/delete', [AuthController::class, 'deleteUserAccount']);
    Route::get('/auths/user', [AuthController::class, 'getAllAuthData']);

});

Route::apiResource('/students', StudentController::class);
//Route::apiResource('/auths', AuthController::class);

