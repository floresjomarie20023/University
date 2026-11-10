# ORM Design Documentation

## Overview

The University CRUD application includes a custom-built Object-Relational Mapping (ORM) layer that provides an elegant, expressive syntax for database interactions while maintaining the simplicity and performance of raw PDO.

## Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                     Application Layer                        │
│  Controllers → Models (BaseModel) → ORM → QueryBuilder      │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Database Layer                          │
│  Database (Singleton PDO) ← Prepared Statements ← MySQL     │
└─────────────────────────────────────────────────────────────┘
```

### Core Components

1. **Database** - Singleton PDO connection manager
2. **QueryBuilder** - Fluent SQL query construction
3. **ORM** - Model-specific query execution and hydration
4. **BaseModel** - Base class for all models with static ORM access

---

## Database Class (`Core\Database`)

### Purpose
Manages a single PDO connection instance throughout the application lifecycle.

### Features
- **Singleton Pattern** - Ensures one connection per request
- **Lazy Initialization** - Connects only when first needed
- **Configuration-Driven** - Reads from `Config\DatabaseConfig`
- **Exception Handling** - Wraps PDO exceptions with context

### Usage
```php
$pdo = Database::getInstance();
// Returns PDO instance with configured options
```

### Configuration
```php
// app/config/DatabaseConfig.php
return [
    'host' => 'localhost',
    'port' => 3306,
    'dbname' => 'university_crud',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ],
];
```

---

## QueryBuilder Class (`Core\QueryBuilder`)

### Purpose
Provides a fluent interface for building SQL queries programmatically.

### Supported Operations
- **SELECT** - With columns, distinct, joins, where, group by, having, order by, limit, offset
- **INSERT** - With automatic placeholder binding
- **UPDATE** - With where clauses
- **DELETE** - With where clauses

### Where Conditions
```php
// Basic comparisons
->where('column', '=', 'value')
->where('column', '!=', 'value')
->where('column', '>', 10)
->where('column', '>=', 10)
->where('column', '<', 10)
->where('column', '<=', 10)
->where('column', 'LIKE', '%value%')

// IN / NOT IN
->whereIn('column', [1, 2, 3])
->whereNotIn('column', [1, 2, 3])

// NULL checks
->whereNull('column')
->whereNotNull('column')

// BETWEEN
->whereBetween('column', [1, 100])

// Logical grouping
->orWhere('column', '=', 'value')
```

### Joins
```php
->join('table2', 'table1.id', '=', 'table2.table1_id')
->leftJoin('table2', 'table1.id', '=', 'table2.table1_id')
->rightJoin('table2', 'table1.id', '=', 'table2.table1_id')
```

### Example
```php
$query = (new QueryBuilder())
    ->table('users')
    ->select(['id', 'name', 'email'])
    ->where('is_active', '=', true)
    ->where('role', '!=', 'admin')
    ->orderBy('created_at', 'desc')
    ->limit(10);

$sql = $query->toSql();
// SELECT `id`, `name`, `email` FROM `users` WHERE `is_active` = ? AND `role` != ? ORDER BY `created_at` DESC LIMIT 10

$bindings = $query->getBindings();
// [true, 'admin']
```

---

## ORM Class (`Core\ORM`)

### Purpose
Model-specific query execution, result hydration, and CRUD operations.

### Constructor
```php
$orm = new ORM(User::class);
```

The ORM reads model metadata via reflection:
- `$table` - Table name (defaults to pluralized class name)
- `$primaryKey` - Primary key column (defaults to 'id')
- `$fillable` - Mass-assignable attributes
- `$hidden` - Attributes hidden from serialization
- `$casts` - Attribute type casting rules
- `$dates` - Date columns for DateTime conversion

### Query Methods

```php
// Get all
$orm->all(['id', 'name']);

// Find by primary key
$orm->find(1);
$orm->findOrFail(1); // Throws ModelNotFoundException

// First matching
$orm->first();

// Chained queries
$orm->where('role', '=', 'admin')
    ->where('is_active', '=', true)
    ->orderBy('name')
    ->limit(5)
    ->get(['id', 'name']);

// Pagination
$orm->paginate(15, 1); // perPage, page
```

### CRUD Methods

```php
// Create
$user = $orm->create([
    'name' => 'John',
    'email' => 'john@example.com',
    'password' => 'hashed_password',
]);

// Update
$user = $orm->update(1, ['name' => 'Jane']);

// Delete
$deleted = $orm->delete(1);

// Aggregates
$count = $orm->count();
$exists = $orm->exists();
$ids = $orm->pluck('id');
```

### Attribute Casting

The ORM automatically casts attributes based on the `$casts` array:

| Cast Type | PHP Type | Storage Format |
|-----------|----------|----------------|
| `int` / `integer` | `int` | Integer |
| `float` / `double` | `float` | Decimal |
| `bool` / `boolean` | `bool` | TinyInt (0/1) |
| `array` / `json` | `array` | JSON string |
| `object` | `stdClass` | JSON string |
| `date` | `DateTime` | DATE |
| `datetime` | `DateTime` | DATETIME |
| `timestamp` | `int` | TIMESTAMP |

### Model Hydration

Results are hydrated into model instances:
```php
$user = $orm->find(1);
// Returns User object with all properties populated

// Properties are set via reflection (public/protected)
echo $user->name; // "John"
echo $user->created_at; // "2024-01-15 10:00:00" (cast to DateTime if in $dates)
```

### Serialization

```php
$user = $orm->find(1);

$array = $orm->toArray($user);
// ['id' => 1, 'name' => 'John', 'email' => 'john@example.com', ...]

$json = $orm->toJson($user);
// {"id":1,"name":"John","email":"john@example.com",...}

// Hidden fields (password, remember_token) are excluded
```

---

## BaseModel Class (`Core\BaseModel`)

### Purpose
Provides static access to ORM functionality and instance methods for active record pattern.

### Static Methods (Query Interface)

```php
// All delegate to ORM
User::all()
User::find(1)
User::findOrFail(1)
User::first()
User::where('role', '=', 'admin')
User::whereIn('id', [1,2,3])
User::orderBy('name')
User::limit(10)
User::get()
User::paginate(10, 1)
User::create([...])
User::update(1, [...])
User::delete(1)
User::count()
User::exists()
User::pluck('name')
```

### Instance Methods (Active Record)

```php
$user = User::find(1);

// Update
$user->name = 'New Name';
$user->save(); // Calls update()

// Or
$user->fill(['name' => 'New Name'])->save();

// Delete
$user->delete();

// Serialization
$user->toArray();
$user->toJson();
```

### Model Definition

```php
class User extends BaseModel
{
    protected static string $table = 'users';
    protected static string $primaryKey = 'id';
    protected static array $fillable = ['name', 'email', 'password', 'role', 'is_active'];
    protected static array $hidden = ['password', 'remember_token'];
    protected static array $casts = [
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected static array $dates = ['created_at', 'updated_at'];

    // Properties MUST be public for hydration
    public int $id;
    public string $name;
    public string $email;
    public string $password;
    public string $role = 'user';
    public bool $is_active = true;
    public ?string $remember_token = null;
    public string $created_at;
    public string $updated_at;

    // Custom methods
    public function setPassword(string $password): self { ... }
    public function checkPassword(string $password): bool { ... }
}
```

---

## PSR-4 Autoloading Implementation

### Composer Configuration

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/",
            "Core\\": "app/core/",
            "Config\\": "app/config/"
        }
    }
}
```

### Namespace Mapping

| Namespace Prefix | Base Directory | Example Class | File Path |
|-----------------|----------------|---------------|-----------|
| `App\` | `app/` | `App\Models\User` | `app/Models/User.php` |
| `App\Controllers\` | `app/Controllers/` | `App\Controllers\UserController` | `app/Controllers/UserController.php` |
| `Core\` | `app/core/` | `Core\Database` | `app/core/Database.php` |
| `Config\` | `app/config/` | `Config\DatabaseConfig` | `app/config/DatabaseConfig.php` |

### Autoloader Generation

```bash
composer dump-autoload
# Or with optimization:
composer dump-autoload -o
```

### Class Loading Flow

1. PHP encounters `new App\Models\User()`
2. Composer autoloader converts namespace to path: `App\Models\User` → `app/Models/User.php`
3. File is included and class is defined
4. Subsequent uses are cached in OPcache

---

## Security Considerations

### SQL Injection Prevention

All queries use **prepared statements** with parameter binding:

```php
// QueryBuilder builds SQL with placeholders
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]); // Bound safely

// Never use string concatenation
// BAD: "SELECT * FROM users WHERE email = '$email'"
```

### Mass Assignment Protection

The `$fillable` array defines which attributes can be mass-assigned:

```php
// Controller
$data = $_POST; // User input
$user = User::create($data); // Only fillable fields are used

// Model
protected static array $fillable = ['name', 'email']; // 'password' NOT fillable
```

### Password Handling

```php
// In User model
public function setPassword(string $password): self
{
    $this->password = password_hash($password, PASSWORD_BCRYPT);
    return $this;
}

public function checkPassword(string $password): bool
{
    return password_verify($password, $this->password);
}
```

---

## Extending the ORM

### Adding Custom Query Scopes

```php
class Post extends BaseModel
{
    // Add to BaseModel or create a trait
    public static function published(): array
    {
        return static::where('status', '=', 'published')
            ->where('published_at', '<=', date('Y-m-d H:i:s'))
            ->orderBy('published_at', 'desc')
            ->get();
    }
}
```

### Adding Relationships

```php
class User extends BaseModel
{
    public function posts(): array
    {
        return Post::where('user_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    
    public function latestPost(): ?Post
    {
        return Post::where('user_id', '=', $this->id)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
```

### Custom Casting

```php
protected static array $casts = [
    'settings' => 'json',
    'metadata' => 'object',
    'price' => 'float',
];
```

---

## Performance Considerations

1. **Connection Reuse** - Singleton PDO prevents connection overhead
2. **Prepared Statement Caching** - MySQL caches prepared statements
3. **Selective Columns** - Use `select(['id', 'name'])` instead of `*`
4. **Indexes** - Database schema includes indexes on foreign keys and common query columns
5. **Pagination** - Large datasets use LIMIT/OFFSET instead of loading all records

---

## Error Handling

```php
try {
    $user = User::findOrFail(999);
} catch (ModelNotFoundException $e) {
    // Handle not found
}

try {
    User::create($data);
} catch (ValidationException $e) {
    // Handle validation errors
} catch (\PDOException $e) {
    // Handle database errors (unique constraint, etc.)
}
```

---

## Testing the ORM

```php
// Create
$user = User::create([
    'name' => 'Test User',
    'email' => 'test@example.com',
    'password' => 'password123',
]);

// Read
$found = User::find($user->id);
assert($found->name === 'Test User');

// Update
User::update($user->id, ['name' => 'Updated Name']);
$updated = User::find($user->id);
assert($updated->name === 'Updated Name');

// Delete
User::delete($user->id);
assert(User::find($user->id) === null);
```

---

## Future Enhancements

1. **Eager Loading** - `with(['posts'])` to avoid N+1 queries
2. **Transaction Support** - `Database::transaction(fn() => ...)`
3. **Soft Deletes** - `deleted_at` column with global scope
4. **Events/Observers** - `creating`, `created`, `updating`, `updated`, `deleting`, `deleted`
5. **Scopes** - Global and local query scopes
6. **Mutators/Accessors** - Custom get/set logic
7. **Serialization Groups** - Different arrays for API vs internal use