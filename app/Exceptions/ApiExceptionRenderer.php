<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class ApiExceptionRenderer
{
    public function renderNotFound(NotFoundHttpException $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*')) {
            return null;
        }

        return response()->json(['message' => 'Recurso não encontrado.'], 404);
    }

    public function renderFallback(Throwable $e, Request $request): ?JsonResponse
    {
        if (! $request->is('api/*') || $e instanceof ValidationException) {
            return null;
        }

        if ($e instanceof HttpExceptionInterface) {
            return response()->json([
                'message' => $e->getMessage() ?: 'Erro ao processar a requisição.',
            ], $e->getStatusCode());
        }

        return response()->json(['message' => 'Erro interno do servidor.'], 500);
    }
}
