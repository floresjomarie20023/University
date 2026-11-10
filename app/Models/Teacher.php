<?php

namespace App\Models;

use Core\BaseModel;

class Teacher extends BaseModel
{
    protected static string $table = 'teachers';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'employee_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'department',
        'position',
        'hire_date',
        'address',
        'status',
    ];
    protected static array $casts = [];

    public int $id = 0;
    public string $employee_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $gender = 'other';
    public ?string $date_of_birth = null;
    public string $department = '';
    public string $position = '';
    public ?string $hire_date = null;
    public ?string $address = null;
    public string $status = 'active';
    public string $created_at = '';
    public string $updated_at = '';

    public function getFullName(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function getInitials(): string
    {
        return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'on_leave' => '<span class="badge bg-warning text-dark">On Leave</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getSchedules(): array
    {
        return Schedule::where('teacher_id', '=', $this->id)
            ->orderBy('day_of_week')
            ->get();
    }
}
