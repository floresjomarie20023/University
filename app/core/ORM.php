<?php

namespace Core;

use PDO;
use PDOException;
use ReflectionClass;
use ReflectionProperty;

class ORM
{
    protected PDO $db;
    protected QueryBuilder $queryBuilder;
    protected string $modelClass;
    protected string $table;
    protected string $primaryKey = 'id';
    protected array $fillable = [];
    protected array $hidden = [];
    protected array $casts = [];
    protected array $dates = [];
    protected array $relations = [];

    public function __construct(string $modelClass)
    {
        $this->db = Database::getInstance();
        $this->queryBuilder = new QueryBuilder();
        $this->modelClass = $modelClass;

        $reflection = new ReflectionClass($modelClass);
        $this->table = $reflection->getStaticPropertyValue('table') ?? $this->guessTableName($modelClass);
        $this->primaryKey = $reflection->getStaticPropertyValue('primaryKey') ?? 'id';
        $this->fillable = $reflection->getStaticPropertyValue('fillable') ?? [];
        $this->hidden = $reflection->getStaticPropertyValue('hidden') ?? [];
        $this->casts = $reflection->getStaticPropertyValue('casts') ?? [];
        $this->dates = $reflection->getStaticPropertyValue('dates') ?? [];
        $this->relations = $reflection->getStaticPropertyValue('relations') ?? [];
    }

    protected function guessTableName(string $modelClass): string
    {
        $className = (new ReflectionClass($modelClass))->getShortName();
        return strtolower($className) . 's';
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder->table($this->table);
    }

    public function all(array $columns = ['*']): array
    {
        $query = $this->queryBuilder->table($this->table)->select($columns);
        return $this->executeQuery($query)->fetchAll();
    }

    public function find(mixed $id, array $columns = ['*']): ?object
    {
        $query = $this->queryBuilder
            ->table($this->table)
            ->select($columns)
            ->where($this->primaryKey, '=', $id);

        $result = $this->executeQuery($query)->fetch();
        return $result ? $this->hydrateModel($result) : null;
    }

    public function findOrFail(mixed $id, array $columns = ['*']): object
    {
        $model = $this->find($id, $columns);
        if ($model === null) {
            throw new ModelNotFoundException("Model {$this->modelClass} with ID {$id} not found");
        }
        return $model;
    }

    public function first(array $columns = ['*']): ?object
    {
        $query = $this->queryBuilder
            ->table($this->table)
            ->select($columns)
            ->limit(1);

        $result = $this->executeQuery($query)->fetch();
        return $result ? $this->hydrateModel($result) : null;
    }

    public function where(string $column, string $operator, mixed $value): self
    {
        $this->queryBuilder->where($column, $operator, $value);
        return $this;
    }

    public function whereIn(string $column, array $values): self
    {
        $this->queryBuilder->whereIn($column, $values);
        return $this;
    }

    public function whereNotIn(string $column, array $values): self
    {
        $this->queryBuilder->whereNotIn($column, $values);
        return $this;
    }

    public function whereNull(string $column): self
    {
        $this->queryBuilder->whereNull($column);
        return $this;
    }

    public function whereNotNull(string $column): self
    {
        $this->queryBuilder->whereNotNull($column);
        return $this;
    }

    public function whereBetween(string $column, array $values): self
    {
        $this->queryBuilder->whereBetween($column, $values);
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        $this->queryBuilder->orWhere($column, $operator, $value);
        return $this;
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->queryBuilder->orderBy($column, $direction);
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->queryBuilder->limit($limit);
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->queryBuilder->offset($offset);
        return $this;
    }

    public function get(array $columns = ['*']): array
    {
        $query = $this->queryBuilder->table($this->table)->select($columns);
        $results = $this->executeQuery($query)->fetchAll();
        return array_map(fn($row) => $this->hydrateModel($row), $results);
    }

    public function paginate(int $perPage = 10, int $page = 1, array $columns = ['*']): array
    {
        $perPage = max(1, min($perPage, 100));
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $this->queryBuilder;
        $countBuilder->table($this->table)->select(['COUNT(*) as total']);
        $total = (int)($this->executeQuery($countBuilder)->fetch()['total'] ?? 0);

        $query = $this->queryBuilder->table($this->table)->select($columns)->limit($perPage)->offset($offset);
        $data = array_map(fn($row) => $this->hydrateModel($row), $this->executeQuery($query)->fetchAll());

        $lastPage = (int)ceil($total / $perPage);

        return [
            'data' => $data,
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
            ],
        ];
    }

    public function create(array $data): object
    {
        $data = $this->filterFillable($data);
        $data = $this->prepareDataForInsert($data);

        $query = $this->queryBuilder->reset()->table($this->table)->insert($data);
        $stmt = $this->executeQuery($query);

        $id = $this->db->lastInsertId();
        $this->queryBuilder->reset();
        return $this->findOrFail($id);
    }

    public function update(mixed $id, array $data): ?object
    {
        $model = $this->find($id);
        if ($model === null) {
            return null;
        }

        $data = $this->filterFillable($data);
        $data = $this->prepareDataForUpdate($data);

        $query = $this->queryBuilder
            ->reset()
            ->table($this->table)
            ->update($data)
            ->where($this->primaryKey, '=', $id);

        $this->executeQuery($query);
        $this->queryBuilder->reset();
        return $this->find($id);
    }

    public function delete(mixed $id): bool
    {
        $query = $this->queryBuilder
            ->reset()
            ->table($this->table)
            ->delete()
            ->where($this->primaryKey, '=', $id);

        $stmt = $this->executeQuery($query);
        return $stmt->rowCount() > 0;
    }

    public function forceDelete(mixed $id): bool
    {
        return $this->delete($id);
    }

    public function count(): int
    {
        $query = $this->queryBuilder->table($this->table)->select(['COUNT(*) as count']);
        return (int)($this->executeQuery($query)->fetch()['count'] ?? 0);
    }

    public function exists(): bool
    {
        return $this->count() > 0;
    }

    public function pluck(string $column): array
    {
        $query = $this->queryBuilder->table($this->table)->select([$column]);
        $results = $this->executeQuery($query)->fetchAll();
        return array_column($results, $column);
    }

    protected function executeQuery(QueryBuilder $query): \PDOStatement
    {
        $sql = $query->toSql();
        $bindings = $query->getBindings();

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($bindings);
            return $stmt;
        } catch (PDOException $e) {
            error_log("SQL Error: {$e->getMessage()} | Query: {$sql} | Bindings: " . json_encode($bindings));
            throw new \RuntimeException('Database query failed: ' . $e->getMessage(), 0, $e);
        }
    }

    protected function hydrateModel(array $data): object
    {
        $model = new $this->modelClass();

        $reflection = new ReflectionClass($this->modelClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);

        foreach ($properties as $property) {
            $name = $property->getName();
            if (array_key_exists($name, $data)) {
                $value = $this->castAttribute($name, $data[$name]);
                $property->setValue($model, $value);
            }
        }

        return $model;
    }

    protected function filterFillable(array $data): array
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    protected function prepareDataForInsert(array $data): array
    {
        $data = $this->castAttributesForStorage($data);
        $data['created_at'] = $data['created_at'] ?? date('Y-m-d H:i:s');
        $data['updated_at'] = $data['updated_at'] ?? date('Y-m-d H:i:s');
        return $data;
    }

    protected function prepareDataForUpdate(array $data): array
    {
        $data = $this->castAttributesForStorage($data);
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $data;
    }

    protected function castAttributesForStorage(array $data): array
    {
        foreach ($data as $key => $value) {
            if (isset($this->casts[$key])) {
                $data[$key] = $this->castForStorage($key, $value);
            }
        }
        return $data;
    }

    protected function castAttribute(string $key, mixed $value): mixed
    {
        if (!isset($this->casts[$key])) {
            return $value;
        }

        return match ($this->casts[$key]) {
            'int', 'integer' => (int)$value,
            'float', 'double' => (float)$value,
            'bool', 'boolean' => (bool)$value,
            'array', 'json' => is_string($value) ? json_decode($value, true) : $value,
            'object' => is_string($value) ? json_decode($value) : $value,
            'date' => $value instanceof \DateTime ? $value : new \DateTime($value),
            'datetime' => $value instanceof \DateTime ? $value : new \DateTime($value),
            'timestamp' => $value instanceof \DateTime ? $value->getTimestamp() : (int)$value,
            default => $value,
        };
    }

    protected function castForStorage(string $key, mixed $value): mixed
    {
        return match ($this->casts[$key]) {
            'array', 'json', 'object' => json_encode($value),
            'date', 'datetime' => $value instanceof \DateTime ? $value->format('Y-m-d H:i:s') : $value,
            'timestamp' => $value instanceof \DateTime ? $value->getTimestamp() : $value,
            default => $value,
        };
    }

    public function toArray(object $model): array
    {
        $reflection = new ReflectionClass($this->modelClass);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_PROTECTED);
        $data = [];

        foreach ($properties as $property) {
            $name = $property->getName();
            if (in_array($name, $this->hidden)) {
                continue;
            }
            $data[$name] = $property->getValue($model);
        }

        return $data;
    }

    public function toJson(object $model): string
    {
        return json_encode($this->toArray($model), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}

class ModelNotFoundException extends \Exception
{
}