<?php

namespace App\Models;

use Core\BaseModel;

class Discipline extends BaseModel
{
    protected static string $table = 'disciplines';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'student_id',
        'teacher_id',
        'incident_date',
        'category',
        'description',
        'action_taken',
        'status',
        'resolved_at',
    ];
    protected static array $casts = [];

    public int $id = 0;
    public int $student_id = 0;
    public ?int $teacher_id = null;
    public string $incident_date = '';
    public string $category = 'minor';
    public string $description = '';
    public ?string $action_taken = null;
    public string $status = 'open';
    public ?string $resolved_at = null;
    public string $created_at = '';
    public string $updated_at = '';

    public static function categories(): array
    {
        return ['minor', 'major', 'severe'];
    }

    public static function statuses(): array
    {
        return ['open', 'resolved', 'appealed'];
    }

    public function getStudent(): ?Student
    {
        return Student::find($this->student_id);
    }

    public function getTeacher(): ?Teacher
    {
        if ($this->teacher_id === null) {
            return null;
        }
        return Teacher::find($this->teacher_id);
    }

    public function getCategoryBadge(): string
    {
        return match ($this->category) {
            'minor' => '<span class="badge bg-info text-dark">Minor</span>',
            'major' => '<span class="badge bg-warning text-dark">Major</span>',
            'severe' => '<span class="badge bg-danger">Severe</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->category) . '</span>',
        };
    }

    public function getStatusBadge(): string
    {
        return match ($this->status) {
            'open' => '<span class="badge bg-warning text-dark">Open</span>',
            'resolved' => '<span class="badge bg-success">Resolved</span>',
            'appealed' => '<span class="badge bg-secondary">Appealed</span>',
            default => '<span class="badge bg-light text-dark">' . ucfirst($this->status) . '</span>',
        };
    }
}
