<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-people me-1"></i>Students</h1>
        <p class="text-muted mb-0">Manage student records and enrollment details.</p>
    </div>
    <a href="/students/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Student</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/students" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label small text-muted">Search</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Name, student number or email">
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label small text-muted">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All statuses</option>
                    <?php foreach ($statuses as $option): ?>
                        <option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                <?php if ($search !== '' || $status !== ''): ?>
                    <a href="/students" class="btn btn-outline-secondary">Clear</a>
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
                <th>Student</th>
                <th>Student No.</th>
                <th>Grade &amp; Section</th>
                <th>Email</th>
                <th>Status</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($students as $student): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="avatar-circle bg-primary-subtle text-primary"><?= htmlspecialchars($student->getInitials()) ?></span>
                            <div>
                                <a href="/students/<?= (int)$student->id ?>" class="text-decoration-none fw-semibold d-block"><?= htmlspecialchars($student->getFullName()) ?></a>
                                <span class="text-muted small"><?= htmlspecialchars($student->gender) ?></span>
                            </div>
                        </div>
                    </td>
                    <td class="text-nowrap"><?= htmlspecialchars($student->student_number) ?></td>
                    <td><?= htmlspecialchars($student->grade_level . ' - ' . $student->section) ?></td>
                    <td><a href="mailto:<?= htmlspecialchars($student->email) ?>" class="text-decoration-none"><?= htmlspecialchars($student->email) ?></a></td>
                    <td><?= $student->getStatusBadge() ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/students/<?= (int)$student->id ?>" class="btn btn-outline-primary" title="View"><i class="bi bi-eye"></i></a>
                            <a href="/students/<?= (int)$student->id ?>/edit" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/students/<?= (int)$student->id ?>" onsubmit="return confirm('Delete this student and all related discipline records?')">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($students)): ?>
                <tr><td colspan="6" class="text-center text-muted py-5">No students found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
</div>
