<?php if (!empty($pagination['total']) && $pagination['total'] > $pagination['per_page']): ?>
    <?php
    $currentPage = (int)$pagination['current_page'];
    $lastPage = (int)$pagination['last_page'];
    $queryParams = $_GET;
    unset($queryParams['page']);
    $queryString = !empty($queryParams) ? '?' . http_build_query($queryParams) . '&' : '?';
    ?>
    <div class="card-footer bg-white">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                Showing <?= (int)$pagination['from'] ?>–<?= (int)$pagination['to'] ?> of <?= (int)$pagination['total'] ?>
            </div>
            <nav aria-label="Pagination">
                <ul class="pagination pagination-sm mb-0">
                    <li class="page-item <?= $currentPage <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars($queryString) ?>page=<?= max(1, $currentPage - 1) ?>">Previous</a>
                    </li>
                    <?php for ($i = max(1, $currentPage - 2); $i <= min($lastPage, $currentPage + 2); $i++): ?>
                        <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="<?= htmlspecialchars($queryString) ?>page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $currentPage >= $lastPage ? 'disabled' : '' ?>">
                        <a class="page-link" href="<?= htmlspecialchars($queryString) ?>page=<?= min($lastPage, $currentPage + 1) ?>">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
<?php endif; ?>
