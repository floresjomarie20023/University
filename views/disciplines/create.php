<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-file-earmark-plus me-1"></i>File Discipline Record</h1>
        <p class="text-muted mb-0">Log a new discipline incident for a student.</p>
    </div>
    <a href="/disciplines" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Records</a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <form method="POST" action="/disciplines" novalidate>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="card mb-4">
                <div class="card-header py-3"><h2 class="h6 mb-0">Incident Information</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="student_id" class="form-label">Student <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['student_id']) ? 'is-invalid' : '' ?>" id="student_id" name="student_id" required>
                                <option value="">Select student</option>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= (int)$student->id ?>" <?= (string)$old('student_id', $_GET['student_id'] ?? '') === (string)$student->id ? 'selected' : '' ?>><?= htmlspecialchars($student->getFullName() . ' (' . $student->student_number . ')') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['student_id'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['student_id'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="incident_date" class="form-label">Incident Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control <?= isset($errors['incident_date']) ? 'is-invalid' : '' ?>" id="incident_date" name="incident_date" value="<?= htmlspecialchars((string)$old('incident_date', date('Y-m-d'))) ?>" required>
                            <?php if (isset($errors['incident_date'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['incident_date'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['category']) ? 'is-invalid' : '' ?>" id="category" name="category" required>
                                <?php foreach ($categories as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('category', 'minor') === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['category'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['category'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="teacher_id" class="form-label">Reported By</label>
                            <select class="form-select <?= isset($errors['teacher_id']) ? 'is-invalid' : '' ?>" id="teacher_id" name="teacher_id">
                                <option value="">—</option>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= (int)$teacher->id ?>" <?= (string)$old('teacher_id') === (string)$teacher->id ? 'selected' : '' ?>><?= htmlspecialchars($teacher->getFullName()) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['teacher_id'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['teacher_id'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <?php foreach ($statuses as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('status', 'open') === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['status'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-12">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control <?= isset($errors['description']) ? 'is-invalid' : '' ?>" id="description" name="description" rows="4" required><?= htmlspecialchars((string)$old('description')) ?></textarea>
                            <?php if (isset($errors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['description'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="action_taken" class="form-label">Action Taken</label>
                            <textarea class="form-control" id="action_taken" name="action_taken" rows="3"><?= htmlspecialchars((string)$old('action_taken')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Save Record</button>
                <a href="/disciplines" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
