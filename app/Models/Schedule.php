<?php

namespace App\Models;

use Core\BaseModel;

class Schedule extends BaseModel
{
    protected static string $table = 'schedules';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'subject',
        'day_of_week',
        'start_time',
        'end_time',
        'room',
        'grade_level',
        'section',
        'teacher_id',
        'notes',
    ];
    protected static array $casts = [];

    public int $id = 0;
    public string $subject = '';
    public string $day_of_week = '';
    public string $start_time = '';
    public string $end_time = '';
    public ?string $room = null;
    public string $grade_level = '';
    public string $section = '';
    public ?int $teacher_id = null;
    public ?string $notes = null;
    public string $created_at = '';
    public string $updated_at = '';

    public static function days(): array
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    }

    public function getTeacher(): ?Teacher
    {
        if ($this->teacher_id === null) {
            return null;
        }
        return Teacher::find($this->teacher_id);
    }

    public function getTimeRange(): string
    {
        return date('g:i A', strtotime($this->start_time)) . ' - ' . date('g:i A', strtotime($this->end_time));
    }

    public function getDayBadge(): string
    {
        return '<span class="badge bg-primary">' . htmlspecialchars($this->day_of_week) . '</span>';
    }
}
