<?php

namespace App\classes;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Log;

class ApiResponseHelper
{
    public function rollback($e, $message = 'failure in the process')
    {
        DB::rollBack();
        self::throw($e, $message);

    }
    public static function throw($e, $message = 'Failure in the process')
    {
        Log::info($e);
        throw new HttpResponseException(response()->json([
            'message' => $message
        ], 500));
    }
    public static function sendResponse($result, $message = '', $code = 200)
    {
        if ($code === 204) {
            return response()->noContent();
        }
        $response = [
            'success' => true,
            'data' => $result
        ];
        if (!empty($message)) {
            $response['message'] = $message;
        }
        return response()->json($response, $code);
    }
    public static function sendError($message, $errors = null, $code = 400)
    {
        $response = [
            'estado' => 'error', // Mantener consistencia en español
            'mensaje' => $message,
        ];

        if ($errors !== null) { // Verificar explícitamente si $errors no es nulo
            $response['errores'] = $errors;
        }

        return response()->json($response, $code);
    }
}