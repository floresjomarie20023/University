<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-exclamation-triangle me-1"></i>Discipline Records</h1>
        <p class="text-muted mb-0">Track and manage student discipline incidents.</p>
    </div>
    <a href="/disciplines/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>File Record</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/disciplines" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label for="search" class="form-label small text-muted">Search description</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search incidents">
                </div>
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label small text-muted">Category</label>
                <select class="form-select" id="category" name="category">
                    <option value="">All</option>
                    <?php foreach ($categories as $option): ?>
                        <option value="<?= $option ?>" <?= $category === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label small text-muted">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All</option>
                    <?php foreach ($statuses as $option): ?>
                        <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                <?php if ($search !== '' || $status !== '' || $category !== ''): ?>
                    <a href="/disciplines" class="btn btn-outline-secondary">Clear</a>
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
                <th>Date</th>
                <th>Student</th>
                <th>Category</th>
                <th>Description</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $record): ?>
                <?php $student = $record->getStudent(); ?>
                <tr>
                    <td class="text-nowrap"><?= htmlspecialchars(date('M d, Y', strtotime($record->incident_date))) ?></td>
                    <td>
                        <?php if ($student): ?>
                            <a href="/students/<?= (int)$student->id ?>" class="text-decoration-none fw-semibold"><?= htmlspecialchars($student->getFullName()) ?></a>
                            <span class="text-muted small d-block"><?= htmlspecialchars($student->student_number) ?></span>
                        <?php else: ?>
                            <span class="text-muted">Deleted student</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $record->getCategoryBadge() ?></td>
                    <td class="text-truncate" style="max-width: 320px;"><?= htmlspecialchars($record->description) ?></td>
                    <td><?= $record->getStatusBadge() ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/disciplines/<?= (int)$record->id ?>" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="/disciplines/<?= (int)$record->id ?>/edit" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/disciplines/<?= (int)$record->id ?>" onsubmit="return confirm('Delete this discipline record?')">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($records)): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No discipline records found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
</div>
