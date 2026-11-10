<?php

namespace App\Models;

use Core\BaseModel;

class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];
    protected static array $hidden = [
        'password',
        'remember_token',
    ];
    protected static array $casts = [
        'is_active' => 'boolean',
    ];
    protected static array $dates = [
        'created_at',
        'updated_at',
    ];

    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role = 'user';
    public bool $is_active = true;
    public ?string $remember_token = null;
    public string $created_at;
    public string $updated_at;

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);
        return $this;
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function getRoleLabel(): string
    {
        return match ($this->role) {
            'admin' => 'Administrator',
            'teacher' => 'Teacher',
            'moderator' => 'Moderator',
            default => 'User',
        };
    }

    public static function findByEmail(string $email): ?self
    {
        return static::where('email', '=', $email)->first();
    }

    public function getStatusBadge(): string
    {
        return $this->is_active
            ? '<span class="badge bg-success">Active</span>'
            : '<span class="badge bg-danger">Inactive</span>';
    }
}