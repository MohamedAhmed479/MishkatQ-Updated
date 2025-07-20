<?php

namespace App\Helpers;

class ApiResponse
{
    public static function success($data = null, $message = 'نجاح', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function created($data = null, $message = 'تم الإنشاء بنجاح')
    {
        return self::success($data, $message, 201);
    }

    public static function deleted($message = 'تم الحذف بنجاح')
    {
        return response()->json([
            'status' => true,
            'message' => $message,
        ], 200);
    }

    public static function error($message = 'حدث خطأ', $code = 400, $data = null)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function notFound($message = 'لم يتم العثور على المورد')
    {
        return self::error($message, 404);
    }

    public static function unauthorized($message = 'غير مصرح به')
    {
        return self::error($message, 401);
    }

    public static function forbidden($message = 'محظور')
    {
        return self::error($message, 403);
    }

    public static function validationError($errors, $message = 'خطأ التحقق')
    {
        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $errors,
        ], 422);
    }


    public static function withPagination($paginator, $dataKey = 'data', $message = 'نجاح')
    {
        return response()->json([
            'status' => true,

            'message' => $message,

            $dataKey => $paginator->items(),

            'pagination' => [
                'total' => $paginator->total(),
                'count' => $paginator->count(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'total_pages' => $paginator->lastPage(),
                'first' => $paginator->firstItem(),
                'last' => $paginator->lastItem(),
                'next_page_url' => $paginator->nextPageUrl(),
                'prev_page_url' => $paginator->previousPageUrl(),
            ]
        ]);
    }

}
