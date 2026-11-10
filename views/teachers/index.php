<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-person-workspace me-1"></i>Teachers</h1>
        <p class="text-muted mb-0">Manage faculty records, departments and assignments.</p>
    </div>
    <a href="/teachers/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Teacher</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/teachers" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, employee number, email or department">
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label small text-muted">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All statuses</option>
                    <?php foreach ($statuses as $option): ?>
                        <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $option)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                <?php if ($search !== '' || $status !== ''): ?>
                    <a href="/teachers" class="btn btn-outline-secondary">Clear</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
            <tr>
                <th>Teacher</th>
                <th>Employee No.</th>
                <th>Department</th>
                <th>Position</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($teachers as $teacher): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-circle bg-success-subtle text-success"><?= htmlspecialchars($teacher->getInitials()) ?></span>
                            <div>
                                <a href="/teachers/<?= (int)$teacher->id ?>" class="text-decoration-none fw-semibold d-block"><?= htmlspecialchars($teacher->getFullName()) ?></a>
                                <span class="text-muted small"><?= htmlspecialchars($teacher->email) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap"><?= htmlspecialchars($teacher->employee_number) ?></td>
                    <td><?= htmlspecialchars($teacher->department) ?></td>
                    <td><?= htmlspecialchars($teacher->position) ?></td>
                    <td><?= $teacher->getStatusBadge() ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/teachers/<?= (int)$teacher->id ?>" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="/teachers/<?= (int)$teacher->id ?>/edit" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/teachers/<?= (int)$teacher->id ?>" onsubmit="return confirm('Delete this teacher? Their schedules will keep running without an assigned teacher.')">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($teachers)): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No teachers found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
</div>
