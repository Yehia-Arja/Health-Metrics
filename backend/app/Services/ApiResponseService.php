<?php
namespace App\Services;
class ApiResponseService {
    public static function success($message,$data=null,$code=200) {
        return response()->json([
            'success' => 'true',
            'message' => $message,
            'data' => $data
        ], $code);
    }
    public static function error($message,$data=null,$code=400) {
        return response()->json([
            'success' => 'false',
            'message' => $message,
            'data' => $data
        ], $code);
    }
}