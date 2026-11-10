<?php

namespace App\Models;

use Core\BaseModel;

class Student extends BaseModel
{
    protected static string $table = 'students';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'student_number',
        'first_name',
        'last_name',
        'email',
        'phone',
        'gender',
        'date_of_birth',
        'grade_level',
        'section',
        'guardian_name',
        'guardian_phone',
        'address',
        'status',
    ];
    protected static array $casts = [];

    public int $id = 0;
    public string $student_number = '';
    public string $first_name = '';
    public string $last_name = '';
    public string $email = '';
    public ?string $phone = null;
    public string $gender = 'other';
    public ?string $date_of_birth = null;
    public string $grade_level = '';
    public string $section = '';
    public ?string $guardian_name = null;
    public ?string $guardian_phone = null;
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
            'graduated' => '<span class="badge bg-info text-dark">Graduated</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getDisciplineRecords(): array
    {
        return Discipline::where('student_id', '=', $this->id)
            ->orderBy('incident_date', 'desc')
            ->get();
    }
}
