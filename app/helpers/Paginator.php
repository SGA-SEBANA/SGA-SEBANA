<?php

namespace App\Helpers;

class Paginator
{
    public static function make($model, $method, $filters = [], $page = 1, $limit = 10)
    {
        $page = max((int)$page, 1);

        // Detectar si countAll acepta filtros
        $reflectionCount = new \ReflectionMethod($model, 'countAll');

        if ($reflectionCount->getNumberOfParameters() > 0) {
            $total = $model->countAll($filters);
        } else {
            $total = $model->countAll();
        }

        $totalPages = max(1, (int) ceil($total / $limit));

        if ($page > $totalPages) {
            $page = $totalPages;
        }

        $start = ($page - 1) * $limit;

        // Detectar si el método acepta filtros
        $reflection = new \ReflectionMethod($model, $method);

        if ($reflection->getNumberOfParameters() >= 3) {
            $data = $model->$method($filters, $start, $limit);
        } else {
            $data = $model->$method($start, $limit);
        }

        return [
            'data' => $data,
            'page' => $page,
            'limit' => $limit,
            'start' => $start,
            'totalPaginas' => $totalPages
        ];
    }
}