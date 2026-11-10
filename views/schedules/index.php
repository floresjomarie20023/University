<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-calendar3 me-1"></i>Class Schedule</h1>
        <p class="text-muted mb-0">Weekly timetable for all classes.</p>
    </div>
    <a href="/schedules/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i>Add Schedule</a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/schedules" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label for="search" class="form-label small text-muted">Subject</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search subject">
                </div>
            </div>
            <div class="col-md-3">
                <label for="day" class="form-label small text-muted">Day</label>
                <select class="form-select" id="day" name="day">
                    <option value="">All days</option>
                    <?php foreach ($days as $option): ?>
                        <option value="<?= $option ?>" <?= $day === $option ? 'selected' : '' ?>><?= $option ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
                <?php if ($search !== '' || $day !== ''): ?>
                    <a href="/schedules" class="btn btn-outline-secondary">Clear</a>
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
                <th>Subject</th>
                <th>Day</th>
                <th>Time</th>
                <th>Room</th>
                <th>Class</th>
                <th>Teacher</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($schedules as $schedule): ?>
                <?php $teacher = $schedule->getTeacher(); ?>
                <tr>
                    <td class="fw-semibold"><?= htmlspecialchars($schedule->subject) ?></td>
                    <td><?= $schedule->getDayBadge() ?></td>
                    <td class="text-nowrap"><?= htmlspecialchars($schedule->getTimeRange()) ?></td>
                    <td><?= htmlspecialchars($schedule->room ?: '—') ?></td>
                    <td><?= htmlspecialchars($schedule->grade_level . ' - ' . $schedule->section) ?></td>
                    <td><?= $teacher ? htmlspecialchars($teacher->getFullName()) : '<span class="text-muted">Unassigned</span>' ?></td>
                    <td class="text-end">
                        <div class="btn-group btn-group-sm">
                            <a href="/schedules/<?= (int)$schedule->id ?>/edit" class="btn btn-outline-secondary" title="Edit"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="/schedules/<?= (int)$schedule->id ?>" onsubmit="return confirm('Delete this schedule?')">
                                <input type="hidden" name="_method" value="DELETE">
                                <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">
                                <button type="submit" class="btn btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($schedules)): ?>
                <tr><td colspan="7" class="text-center text-muted py-5">No schedule entries found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php include __DIR__ . '/../partials/pagination.php'; ?>
</div>
