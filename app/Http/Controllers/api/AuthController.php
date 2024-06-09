<?php

namespace App\Http\Controllers\api;

use App\classes\ApiResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Http\Requests\UpdateAuthRequest;
use App\Http\Resources\AuthResource;
use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    private AuthRepositoryInterface $authRepositoryInterface;

    public function __construct(AuthRepositoryInterface $authRepositoryInterface)
    {
        $this->authRepositoryInterface = $authRepositoryInterface;
    }

    private function respondWithTokenAndUser(User $user, string $token, string $message, int $statusCode)
    {
        return ApiResponseHelper::sendResponse(
            [
                'user' => new AuthResource($user),
                'access_token' => $token
            ],
            $message,
            $statusCode
        );
    }
    
    public function register(RegisterAuthRequest $request)
    {
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ];
    
        DB::beginTransaction();
    
        try {
            $user = $this->authRepositoryInterface->store($data);
            $token = $user->createToken('api_token')->plainTextToken;
            DB::commit();
            return $this->respondWithTokenAndUser($user, $token, 'Usuario registrado exitosamente', 201);
        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::sendError('Error al registrar usuario', $ex->getMessage(), 500);
        }
    }
    
    public function login(LoginAuthRequest $request)
    {
        try {
            $credentials = $request->validated();

            if (!User::where('email', $credentials['email'])->exists()) {
                return ApiResponseHelper::sendError('Credenciales inválidas', ['email' => 'El correo electrónico no está registrado.'], 401);
            }

            if (!Auth::attempt($credentials)) {
                return ApiResponseHelper::sendError('Credenciales inválidas', ['password' => 'La contraseña es incorrecta.'], 401);
            }
            $user = Auth::user();
            $token = $user->createToken('api_token')->plainTextToken;

            return ApiResponseHelper::sendResponse([
                'user' => new AuthResource($user),
                'token' => $token,
            ], 'Inicio de sesión exitoso', 200);

        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::sendError('Error al iniciar sesión', $ex->getMessage(), 500);
        }
    }
    public function updateUser(UpdateAuthRequest $request)
    {
        try {
            $user = Auth::user();

            $validatedData = $request->validated();

            $user->update($validatedData);

            return ApiResponseHelper::sendResponse(new AuthResource($user), 'Usuario actualizado exitosamente', 200);

        } catch (\Exception $ex) {
            DB::rollBack();
            return ApiResponseHelper::sendError('Error al actualizar el usuario', $ex->getMessage(), 500);
        }
    }
    public function logoutUser(Request $request)
    {
        try {
            $user = $request->user();

            $user->tokens()->delete();

            return ApiResponseHelper::sendResponse(null, 'Cierre de sesión exitoso', 200);

        } catch (\Exception $ex) {
            return ApiResponseHelper::sendError('Error al cerrar sesión', $ex->getMessage(), 500);
        }
    }
    public function deleteUserAccount(Request $request)
    {
        try {
            $user = $request->user();

            $user->tokens()->delete();

            $user->delete();

            return ApiResponseHelper::sendResponse(null, 'Cuenta eliminada exitosamente', 200);

        } catch (\Exception $ex) {
            return ApiResponseHelper::sendError('Error al eliminar la cuenta', $ex->getMessage(), 500);
        }
    }
    public function getAllAuthData()
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }
        $user = Auth::user();

        return ApiResponseHelper::sendResponse(new AuthResource($user), '', 200);
    }


}
