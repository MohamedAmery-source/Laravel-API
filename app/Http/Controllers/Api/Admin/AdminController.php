<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class AdminController extends Controller
{
    protected function ensureAdmin(Request $request): ?JsonResponse
    {
        $user = $request->user();

        if (!$user || $user->user_type !== 'admin') {
            return $this->error('غير مسموح. صلاحية المدير فقط.', 403);
        }

        if (!$user->is_active || $user->status !== 'active') {
            return $this->error('حساب المدير غير نشط.', 403);
        }

        return null;
    }

    protected function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 15);

        return max(1, min($perPage, 100));
    }

    protected function paginateMeta($paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ];
    }
}
