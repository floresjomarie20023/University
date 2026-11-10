<?php

namespace Core;

class QueryBuilder
{
    protected string $table = '';
    protected array $columns = ['*'];
    protected array $wheres = [];
    protected array $joins = [];
    protected array $orders = [];
    protected int|null $limit = null;
    protected int|null $offset = null;
    protected array $bindings = [];
    protected string $type = 'select';
    protected array $insertData = [];
    protected array $updateData = [];
    protected bool $distinct = false;
    protected array $groups = [];
    protected array $havings = [];

    public function table(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function select(array|string $columns = ['*']): self
    {
        $this->type = 'select';
        $this->columns = is_array($columns) ? $columns : func_get_args();
        return $this;
    }

    public function distinct(): self
    {
        $this->distinct = true;
        return $this;
    }

    public function insert(array $data): self
    {
        $this->type = 'insert';
        $this->insertData = $data;
        return $this;
    }

    public function update(array $data): self
    {
        $this->type = 'update';
        $this->updateData = $data;
        return $this;
    }

    public function delete(): self
    {
        $this->type = 'delete';
        return $this;
    }

    public function where(string $column, string $operator, mixed $value, string $boolean = 'and'): self
    {
        $this->wheres[] = [
            'type' => 'basic',
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
        return $this;
    }

    public function whereIn(string $column, array $values, string $boolean = 'and', bool $not = false): self
    {
        if (empty($values)) {
            $this->wheres[] = [
                'type' => 'raw',
                'sql' => $not ? '1=1' : '1=0',
                'boolean' => $boolean,
            ];
            return $this;
        }

        $placeholders = implode(',', array_fill(0, count($values), '?'));
        $this->wheres[] = [
            'type' => 'in',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean,
            'not' => $not,
        ];
        return $this;
    }

    public function whereNotIn(string $column, array $values, string $boolean = 'and'): self
    {
        return $this->whereIn($column, $values, $boolean, true);
    }

    public function whereNull(string $column, string $boolean = 'and', bool $not = false): self
    {
        $this->wheres[] = [
            'type' => 'null',
            'column' => $column,
            'boolean' => $boolean,
            'not' => $not,
        ];
        return $this;
    }

    public function whereNotNull(string $column, string $boolean = 'and'): self
    {
        return $this->whereNull($column, $boolean, true);
    }

    public function whereBetween(string $column, array $values, string $boolean = 'and', bool $not = false): self
    {
        $this->wheres[] = [
            'type' => 'between',
            'column' => $column,
            'values' => $values,
            'boolean' => $boolean,
            'not' => $not,
        ];
        return $this;
    }

    public function orWhere(string $column, string $operator, mixed $value): self
    {
        return $this->where($column, $operator, $value, 'or');
    }

    public function join(string $table, string $first, string $operator, string $second, string $type = 'inner'): self
    {
        $this->joins[] = [
            'table' => $table,
            'first' => $first,
            'operator' => $operator,
            'second' => $second,
            'type' => $type,
        ];
        return $this;
    }

    public function leftJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'left');
    }

    public function rightJoin(string $table, string $first, string $operator, string $second): self
    {
        return $this->join($table, $first, $operator, $second, 'right');
    }

    public function orderBy(string $column, string $direction = 'asc'): self
    {
        $this->orders[] = [
            'column' => $column,
            'direction' => strtolower($direction),
        ];
        return $this;
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    public function offset(int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function groupBy(string|array $columns): self
    {
        $this->groups = array_merge($this->groups, is_array($columns) ? $columns : [$columns]);
        return $this;
    }

    public function having(string $column, string $operator, mixed $value, string $boolean = 'and'): self
    {
        $this->havings[] = [
            'column' => $column,
            'operator' => $operator,
            'value' => $value,
            'boolean' => $boolean,
        ];
        return $this;
    }

    public function toSql(): string
    {
        return match ($this->type) {
            'select' => $this->buildSelect(),
            'insert' => $this->buildInsert(),
            'update' => $this->buildUpdate(),
            'delete' => $this->buildDelete(),
            default => '',
        };
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }

    protected function buildSelect(): string
    {
        $this->bindings = [];
        $sql = 'SELECT ';

        if ($this->distinct) {
            $sql .= 'DISTINCT ';
        }

        $sql .= implode(', ', array_map(fn($col) => $this->wrap($col), $this->columns));
        $sql .= ' FROM ' . $this->wrapTable($this->table);

        if (!empty($this->joins)) {
            foreach ($this->joins as $join) {
                $sql .= sprintf(
                    ' %s JOIN %s ON %s %s %s',
                    strtoupper($join['type']),
                    $this->wrapTable($join['table']),
                    $this->wrap($join['first']),
                    $join['operator'],
                    $this->wrap($join['second'])
                );
            }
        }

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWheres();
        }

        if (!empty($this->groups)) {
            $sql .= ' GROUP BY ' . implode(', ', array_map(fn($col) => $this->wrap($col), $this->groups));
        }

        if (!empty($this->havings)) {
            $sql .= ' HAVING ' . $this->buildHavings();
        }

        if (!empty($this->orders)) {
            $sql .= ' ORDER BY ' . implode(', ', array_map(
                fn($order) => $this->wrap($order['column']) . ' ' . strtoupper($order['direction']),
                $this->orders
            ));
        }

        if ($this->limit !== null) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset !== null) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $sql;
    }

    protected function buildInsert(): string
    {
        if (empty($this->insertData)) {
            return '';
        }

        $columns = array_keys($this->insertData);
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));
        $this->bindings = array_values($this->insertData);

        return sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->wrapTable($this->table),
            implode(', ', array_map(fn($col) => $this->wrap($col), $columns)),
            $placeholders
        );
    }

    protected function buildUpdate(): string
    {
        $this->bindings = [];
        if (empty($this->updateData)) {
            return '';
        }

        $sets = [];
        foreach ($this->updateData as $column => $value) {
            $sets[] = $this->wrap($column) . ' = ?';
            $this->bindings[] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s',
            $this->wrapTable($this->table),
            implode(', ', $sets)
        );

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWheres();
        }

        return $sql;
    }

    protected function buildDelete(): string
    {
        $this->bindings = [];
        $sql = 'DELETE FROM ' . $this->wrapTable($this->table);

        if (!empty($this->wheres)) {
            $sql .= ' WHERE ' . $this->buildWheres();
        }

        return $sql;
    }

    protected function buildWheres(): string
    {
        $sql = '';
        foreach ($this->wheres as $i => $where) {
            if ($i > 0) {
                $sql .= ' ' . strtoupper($where['boolean']) . ' ';
            }

            if ($where['type'] === 'raw') {
                $sql .= $where['sql'];
                continue;
            }

            if ($where['type'] === 'in') {
                $placeholders = implode(',', array_fill(0, count($where['values']), '?'));
                $not = $where['not'] ? 'NOT ' : '';
                $sql .= sprintf('%s %sIN (%s)', $this->wrap($where['column']), $not, $placeholders);
                $this->bindings = array_merge($this->bindings, $where['values']);
                continue;
            }

            if ($where['type'] === 'null') {
                $not = $where['not'] ? 'NOT ' : '';
                $sql .= sprintf('%s %sIS NULL', $this->wrap($where['column']), $not);
                continue;
            }

            if ($where['type'] === 'between') {
                $not = $where['not'] ? 'NOT ' : '';
                $sql .= sprintf(
                    '%s %sBETWEEN ? AND ?',
                    $this->wrap($where['column']),
                    $not
                );
                $this->bindings = array_merge($this->bindings, $where['values']);
                continue;
            }

            $sql .= sprintf(
                '%s %s ?',
                $this->wrap($where['column']),
                $where['operator']
            );
            $this->bindings[] = $where['value'];
        }

        return $sql;
    }

    protected function buildHavings(): string
    {
        $sql = '';
        foreach ($this->havings as $i => $having) {
            if ($i > 0) {
                $sql .= ' ' . strtoupper($having['boolean']) . ' ';
            }

            $sql .= sprintf(
                '%s %s ?',
                $this->wrap($having['column']),
                $having['operator']
            );
            $this->bindings[] = $having['value'];
        }

        return $sql;
    }

    protected function wrap(string $value): string
    {
        if (str_contains($value, '(') || str_contains($value, ')') || preg_match('/\s/', $value) === 1) {
            return $value;
        }

        if (str_contains($value, '.') || $value === '*') {
            return $value;
        }

        return '`' . str_replace('`', '``', $value) . '`';
    }

    protected function wrapTable(string $table): string
    {
        return $this->wrap($table);
    }

    public function reset(): self
    {
        $this->table = '';
        $this->columns = ['*'];
        $this->wheres = [];
        $this->joins = [];
        $this->orders = [];
        $this->limit = null;
        $this->offset = null;
        $this->bindings = [];
        $this->type = 'select';
        $this->insertData = [];
        $this->updateData = [];
        $this->distinct = false;
        $this->groups = [];
        $this->havings = [];
        return $this;
    }
}