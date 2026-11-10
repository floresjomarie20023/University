<?php

namespace Core;

abstract class BaseModel
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';
    protected static array $fillable = [];
    protected static array $hidden = [];
    protected static array $casts = [];
    protected static array $dates = [];
    protected static array $relations = [];

    public static function query(): ORM
    {
        return new ORM(static::class);
    }

    public static function all(array $columns = ['*']): array
    {
        return static::query()->all($columns);
    }

    public static function find(mixed $id, array $columns = ['*']): ?self
    {
        return static::query()->find($id, $columns);
    }

    public static function findOrFail(mixed $id, array $columns = ['*']): self
    {
        return static::query()->findOrFail($id, $columns);
    }

    public static function first(array $columns = ['*']): ?self
    {
        return static::query()->first($columns);
    }

    public static function where(string $column, string $operator, mixed $value): ORM
    {
        return static::query()->where($column, $operator, $value);
    }

    public static function whereIn(string $column, array $values): ORM
    {
        return static::query()->whereIn($column, $values);
    }

    public static function whereNotIn(string $column, array $values): ORM
    {
        return static::query()->whereNotIn($column, $values);
    }

    public static function whereNull(string $column): ORM
    {
        return static::query()->whereNull($column);
    }

    public static function whereNotNull(string $column): ORM
    {
        return static::query()->whereNotNull($column);
    }

    public static function whereBetween(string $column, array $values): ORM
    {
        return static::query()->whereBetween($column, $values);
    }

    public static function orWhere(string $column, string $operator, mixed $value): ORM
    {
        return static::query()->orWhere($column, $operator, $value);
    }

    public static function orderBy(string $column, string $direction = 'asc'): ORM
    {
        return static::query()->orderBy($column, $direction);
    }

    public static function limit(int $limit): ORM
    {
        return static::query()->limit($limit);
    }

    public static function offset(int $offset): ORM
    {
        return static::query()->offset($offset);
    }

    public static function get(array $columns = ['*']): array
    {
        return static::query()->get($columns);
    }

    public static function paginate(int $perPage = 10, int $page = 1, array $columns = ['*']): array
    {
        return static::query()->paginate($perPage, $page, $columns);
    }

    public static function create(array $data): self
    {
        return static::query()->create($data);
    }

    public static function update(mixed $id, array $data): ?self
    {
        return static::query()->update($id, $data);
    }

    public static function delete(mixed $id): bool
    {
        return static::query()->delete($id);
    }

    public static function count(): int
    {
        return static::query()->count();
    }

    public static function exists(): bool
    {
        return static::query()->exists();
    }

    public static function pluck(string $column): array
    {
        return static::query()->pluck($column);
    }

    public function save(): bool
    {
        $orm = static::query();
        $primaryKey = static::$primaryKey;
        $id = $this->$primaryKey ?? null;

        $data = $orm->toArray($this);
        unset($data[$primaryKey]);

        if ($id !== null) {
            $result = static::update($id, $data);
            return $result !== null;
        } else {
            $model = static::create($data);
            foreach ($orm->toArray($model) as $key => $value) {
                $this->$key = $value;
            }
            return true;
        }
    }

    public function deleteRecord(): bool
    {
        $primaryKey = static::$primaryKey;
        $id = $this->$primaryKey ?? null;

        if ($id === null) {
            return false;
        }

        $result = static::delete($id);
        if ($result) {
            $this->$primaryKey = null;
        }
        return $result;
    }

    public function toArray(): array
    {
        return static::query()->toArray($this);
    }

    public function toJson(): string
    {
        return static::query()->toJson($this);
    }

    public function fill(array $data): self
    {
        $orm = static::query();
        $fillable = static::$fillable;

        foreach ($data as $key => $value) {
            if (empty($fillable) || in_array($key, $fillable)) {
                $this->$key = $value;
            }
        }

        return $this;
    }

    public function getAttribute(string $key): mixed
    {
        return $this->$key ?? null;
    }

    public function setAttribute(string $key, mixed $value): self
    {
        $this->$key = $value;
        return $this;
    }

    public function hasAttribute(string $key): bool
    {
        return isset($this->$key);
    }
}