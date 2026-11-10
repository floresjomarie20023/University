<div class="d-flex justify-content-between flex-wrap align-items-center pt-2 pb-2 mb-4 border-bottom">
    <div>
        <h1 class="h3 page-title mb-1"><i class="bi bi-person-plus me-1"></i>Add Teacher</h1>
        <p class="text-muted mb-0">Create a new faculty record.</p>
    </div>
    <a href="/teachers" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back to Teachers</a>
</div>

<div class="row justify-content-center">
    <div class="col-xl-9">
        <form method="POST" action="/teachers" novalidate>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div class="card mb-4">
                <div class="card-header py-3"><h2 class="h6 mb-0">Teacher Information</h2></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="employee_number" class="form-label">Employee Number <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['employee_number']) ? 'is-invalid' : '' ?>" id="employee_number" name="employee_number" value="<?= htmlspecialchars((string)$old('employee_number')) ?>" placeholder="T-2024-001" required>
                            <?php if (isset($errors['employee_number'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['employee_number'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= htmlspecialchars((string)$old('first_name')) ?>" required>
                            <?php if (isset($errors['first_name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['first_name'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= htmlspecialchars((string)$old('last_name')) ?>" required>
                            <?php if (isset($errors['last_name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['last_name'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars((string)$old('email')) ?>" required>
                            <?php if (isset($errors['email'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>" id="phone" name="phone" value="<?= htmlspecialchars((string)$old('phone')) ?>">
                            <?php if (isset($errors['phone'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['phone'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['gender']) ? 'is-invalid' : '' ?>" id="gender" name="gender" required>
                                <option value="">Select</option>
                                <?php foreach ($genders as $option): ?>
                                    <option value="<?= $option ?>" <?= $old('gender') === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['gender'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['gender'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control <?= isset($errors['date_of_birth']) ? 'is-invalid' : '' ?>" id="date_of_birth" name="date_of_birth" value="<?= htmlspecialchars((string)$old('date_of_birth')) ?>">
                            <?php if (isset($errors['date_of_birth'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['date_of_birth'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['department']) ? 'is-invalid' : '' ?>" id="department" name="department" value="<?= htmlspecialchars((string)$old('department')) ?>" placeholder="Science" required>
                            <?php if (isset($errors['department'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['department'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="position" class="form-label">Position <span class="text-danger">*</span></label>
                            <input type="text" class="form-control <?= isset($errors['position']) ? 'is-invalid' : '' ?>" id="position" name="position" value="<?= htmlspecialchars((string)$old('position')) ?>" placeholder="Teacher II" required>
                            <?php if (isset($errors['position'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['position'][0]) ?></div><?php endif; ?>
                        </div>

                        <div class="col-md-4">
                            <label for="hire_date" class="form-label">Hire Date</label>
                            <input type="date" class="form-control <?= isset($errors['hire_date']) ? 'is-invalid' : '' ?>" id="hire_date" name="hire_date" value="<?= htmlspecialchars((string)$old('hire_date')) ?>">
                            <?php if (isset($errors['hire_date'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['hire_date'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>" id="status" name="status" required>
                                <?php foreach ($statuses as $option): ?>
                                    <option value="<?= $option ?>" <?= ($old('status') ?: 'active') === $option ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $option)) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><div class="invalid-feedback"><?= htmlspecialchars($errors['status'][0]) ?></div><?php endif; ?>
                        </div>
                        <div class="col-12">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?= htmlspecialchars((string)$old('address')) ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mb-4">
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i>Save Teacher</button>
                <a href="/teachers" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
