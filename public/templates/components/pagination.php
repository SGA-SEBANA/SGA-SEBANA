<?php

if (!function_exists('render_sga_pagination')) {
    /**
     * Render unified pagination UI.
     */
    function render_sga_pagination(int $page, int $totalPaginas, array $queryParams = []): string
    {
        $page = max(1, $page);
        $totalPaginas = max(1, $totalPaginas);

        if ($totalPaginas <= 1) {
            return '';
        }

        unset($queryParams['page']);

        $buildUrl = static function (int $targetPage) use ($queryParams): string {
            return '?' . http_build_query(array_merge($queryParams, ['page' => $targetPage]));
        };

        $start = max(1, $page - 2);
        $end = min($totalPaginas, $page + 2);

        if ($page <= 3) {
            $end = min($totalPaginas, 5);
        }
        if ($page >= ($totalPaginas - 2)) {
            $start = max(1, $totalPaginas - 4);
        }

        ob_start();
        ?>
        <div class="sga-pagination-wrapper mt-4">
            <nav aria-label="Paginacion">
                <ul class="pagination justify-content-center mb-0">
                    <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page <= 1 ? '#' : $buildUrl($page - 1) ?>" aria-label="Anterior">&laquo;</a>
                    </li>

                    <?php if ($start > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildUrl(1) ?>">1</a>
                        </li>
                        <?php if ($start > 2): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end < $totalPaginas): ?>
                        <?php if ($end < ($totalPaginas - 1)): ?>
                            <li class="page-item disabled">
                                <span class="page-link">...</span>
                            </li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $buildUrl($totalPaginas) ?>"><?= $totalPaginas ?></a>
                        </li>
                    <?php endif; ?>

                    <li class="page-item <?= $page >= $totalPaginas ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= $page >= $totalPaginas ? '#' : $buildUrl($page + 1) ?>" aria-label="Siguiente">&raquo;</a>
                    </li>
                </ul>
            </nav>
            <div class="sga-pagination-meta text-center mt-2">
                Pagina <?= $page ?> de <?= $totalPaginas ?>
            </div>
        </div>
        <?php
        return (string) ob_get_clean();
    }
}

