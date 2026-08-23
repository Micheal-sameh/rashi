# Clean Code Architecture - Laravel Application

## Architecture Overview

```
┌─────────────────────────────────────────────────────────────┐
│                        Presentation Layer                    │
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │ Web Views    │    │ API Routes   │    │ Resources    │  │
│  │ (Blade)      │    │ (JSON)       │    │ (Transform)  │  │
│  └──────────────┘    └──────────────┘    └──────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      Controller Layer                        │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  - Thin Controllers                                   │  │
│  │  - Request Validation                                 │  │
│  │  - Response Formatting                                │  │
│  │  - No Business Logic                                  │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                        Service Layer                         │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  - Business Logic                                     │  │
│  │  - Transaction Management                             │  │
│  │  - Event Dispatching                                  │  │
│  │  - Multi-Repository Coordination                      │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                     Repository Layer                         │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  - Data Access Logic                                  │  │
│  │  - Query Building                                     │  │
│  │  - Eager Loading                                      │  │
│  │  - Database Abstractions                              │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         Model Layer                          │
│  ┌───────────────────────────────────────────────────────┐  │
│  │  - Eloquent Models                                    │  │
│  │  - Relationships                                      │  │
│  │  - Query Scopes                                       │  │
│  │  - Accessors/Mutators                                 │  │
│  └───────────────────────────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                         Database                             │
└─────────────────────────────────────────────────────────────┘
```

## Layer Responsibilities

### 1. Controllers (HTTP Layer)
**Single Responsibility:** Handle HTTP requests and responses

```php
class CompetitionController extends Controller
{
    public function __construct(
        protected CompetitionService $competitionService,
        protected GroupRepository $groupRepository,
    ) {}

    public function index()
    {
        // ✅ Delegate to service
        $competitions = $this->competitionService->index();

        // ✅ Return view/response
        return view('competitions.index', compact('competitions'));
    }
}
```

**What Controllers Should Do:**
- Validate incoming requests (via Form Requests)
- Delegate business logic to services
- Return views or JSON responses
- Handle redirects and flash messages

**What Controllers Should NOT Do:**
- Direct database queries
- Complex business logic
- Transaction management
- Data transformation (use Resources)

---

### 2. Services (Business Logic Layer)
**Single Responsibility:** Implement business rules and coordinate repositories

```php
class CompetitionService
{
    public function __construct(
        protected CompetitionRepository $competitionRepository
    ) {}

    public function store($input, $image)
    {
        // ✅ Business logic
        // ✅ Transaction management
        // ✅ Event dispatching
        DB::beginTransaction();

        $competition = $this->competitionRepository->store($input, $image);

        event(new CompetitionCreated($competition));

        DB::commit();

        return $competition;
    }
}
```

**What Services Should Do:**
- Implement complex business logic
- Coordinate multiple repositories
- Manage database transactions
- Dispatch events
- Handle external API calls

**What Services Should NOT Do:**
- Build queries (delegate to repositories)
- Direct model access
- HTTP-specific logic
- View rendering

---

### 3. Repositories (Data Access Layer)
**Single Responsibility:** Abstract database operations

```php
class CompetitionRepository extends BaseRepository
{
    public function index()
    {
        // ✅ Query building
        // ✅ Eager loading
        // ✅ Filtering
        $query = $this->model->query()
            ->withApiRelations()
            ->where('status', '!=', CompetitionStatus::CANCELLED)
            ->orderBy('start_at');

        return $this->execute($query);
    }
}
```

**What Repositories Should Do:**
- Build database queries
- Handle eager loading
- Implement filtering logic
- Provide data access methods
- Cache query results

**What Repositories Should NOT Do:**
- Business logic
- Transaction management
- Event dispatching
- Complex calculations

---

### 4. Models (Entity Layer)
**Single Responsibility:** Represent database entities

```php
class Competition extends Model
{
    // ✅ Relationships
    public function groups()
    {
        return $this->belongsToMany(Group::class);
    }

    // ✅ Query Scopes
    public function scopeWithApiRelations(Builder $query): Builder
    {
        return $query->with(['media', 'groups']);
    }

    // ✅ Accessors
    public function getStatusLabelAttribute(): string
    {
        return CompetitionStatus::getStringValue($this->status);
    }
}
```

**What Models Should Have:**
- Eloquent relationships
- Query scopes
- Accessors and mutators
- Attribute casting
- Model events

**What Models Should NOT Have:**
- Complex business logic
- HTTP-specific code
- External API calls
- Heavy computations

---

## Design Patterns Implemented

### 1. Repository Pattern
**Purpose:** Abstract data access logic from business logic

```php
// ✅ Good: Use repository
$orders = $this->orderRepository->index($userId, $status);

// ❌ Bad: Direct model access in service
$orders = Order::where('user_id', $userId)->get();
```

### 2. Service Layer Pattern
**Purpose:** Encapsulate business logic

```php
// ✅ Good: Service handles business logic
$this->orderService->store($rewardId, $quantity);

// ❌ Bad: Controller handles business logic
$order = Order::create([...]);
$reward->quantity -= $quantity;
event(new OrderCreated($order));
```

### 3. DTO (Data Transfer Objects)
**Purpose:** Type-safe data transfer between layers

```php
$input = new CompetitionCreateDTO(
    name: $request->name,
    start_at: $request->start_at,
    end_at: $request->end_at,
    groups: $request->groups
);

$this->competitionService->store($input, $request->image);
```

### 4. Dependency Injection
**Purpose:** Loose coupling and testability

```php
class CompetitionController extends Controller
{
    // ✅ Constructor injection
    public function __construct(
        protected CompetitionService $competitionService,
        protected GroupRepository $groupRepository,
    ) {}
}
```

### 5. Query Scopes
**Purpose:** Reusable, chainable query logic

```php
// ✅ Reusable scope
Competition::withApiRelations()
    ->where('status', CompetitionStatus::ACTIVE)
    ->get();
```

---

## SOLID Principles Applied

### Single Responsibility Principle (SRP)
Each class has one reason to change:
- **Controller**: HTTP request changes
- **Service**: Business rule changes
- **Repository**: Data access changes
- **Model**: Database schema changes

### Open/Closed Principle (OCP)
Classes are open for extension but closed for modification:
```php
// Extend through scopes
public function scopeActive(Builder $query): Builder
{
    return $query->where('status', CompetitionStatus::ACTIVE);
}

// Use without modifying base class
Competition::active()->get();
```

### Liskov Substitution Principle (LSP)
Derived classes can substitute base classes:
```php
interface RepositoryInterface
{
    public function findById(int $id): ?Model;
}

// Any repository can be used interchangeably
class BaseRepository implements RepositoryInterface
{
    public function findById(int $id): ?Model { ... }
}
```

### Interface Segregation Principle (ISP)
No client should depend on methods it doesn't use:
```php
// Specific interfaces for specific needs
interface ReadableRepository
{
    public function findById(int $id): ?Model;
    public function all(): Collection;
}

interface WritableRepository
{
    public function create(array $data): Model;
    public function update(int $id, array $data): ?Model;
}
```

### Dependency Inversion Principle (DIP)
Depend on abstractions, not concretions:
```php
// ✅ Depend on interface
public function __construct(
    protected RepositoryInterface $repository
) {}

// ❌ Depend on concrete class
public function __construct(
    protected OrderRepository $repository
) {}
```

---

## Code Organization

```
app/
├── Contracts/                  # Interfaces
│   └── RepositoryInterface.php
├── DTOs/                       # Data Transfer Objects
│   ├── CompetitionCreateDTO.php
│   ├── OrderCreateDTO.php
│   └── UserLoginDTO.php
├── Enums/                      # Enumerations
│   ├── CompetitionStatus.php
│   └── OrderStatus.php
├── Events/                     # Domain Events
│   ├── CompetitionCreated.php
│   └── OrderReceived.php
├── Exceptions/                 # Custom Exceptions
│   └── InsufficientPointsException.php
├── Http/
│   ├── Controllers/           # HTTP Handlers
│   ├── Requests/              # Form Requests
│   └── Resources/             # API Resources
├── Listeners/                 # Event Listeners
├── Models/                    # Eloquent Models
├── Observers/                 # Model Observers
├── Repositories/              # Data Access Layer
│   ├── BaseRepository.php
│   ├── CompetitionRepository.php
│   └── OrderRepository.php
├── Rules/                     # Validation Rules
├── Services/                  # Business Logic Layer
│   ├── CompetitionService.php
│   └── OrderService.php
└── Traits/                    # Reusable Traits
    ├── Auditable.php
    └── HasEagerLoadScopes.php
```

---

## Best Practices Summary

### 1. Always Use Type Hints
```php
// ✅ Good
public function store(CompetitionCreateDTO $input, UploadedFile $image): Competition

// ❌ Bad
public function store($input, $image)
```

### 2. Use Dependency Injection
```php
// ✅ Good
public function __construct(protected OrderService $orderService) {}

// ❌ Bad
public function index() {
    $service = new OrderService();
}
```

### 3. Keep Controllers Thin
```php
// ✅ Good: 3-5 lines per method
public function store(Request $request)
{
    $order = $this->orderService->store($request->validated());
    return redirect()->route('orders.index');
}
```

### 4. Use Query Scopes for Reusability
```php
// ✅ Good
Order::withApiRelations()->pending()->get();

// ❌ Bad
Order::with(['reward', 'user'])->where('status', 'pending')->get();
```

### 5. Eager Load Relationships
```php
// ✅ Good: N+1 prevented
$orders = Order::with(['user', 'reward'])->get();

// ❌ Bad: N+1 problem
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->user->name; // Extra query each iteration
}
```

### 6. Use Transactions for Multi-Step Operations
```php
DB::transaction(function () {
    $order = $this->orderRepository->store($data);
    $this->userRepository->updatePoints($userId, -$points);
    $this->rewardRepository->decrementStock($rewardId);
});
```

### 7. Return Early for Guard Clauses
```php
// ✅ Good
public function store($data)
{
    if (!$this->canStore($data)) {
        throw new ValidationException();
    }

    return $this->repository->create($data);
}

// ❌ Bad: Nested conditions
public function store($data)
{
    if ($this->canStore($data)) {
        return $this->repository->create($data);
    } else {
        throw new ValidationException();
    }
}
```

---

## Testing Strategy

### Unit Tests (Services & Repositories)
```php
public function test_competition_service_creates_competition()
{
    $dto = new CompetitionCreateDTO(...);
    $competition = $this->competitionService->store($dto, $image);

    $this->assertInstanceOf(Competition::class, $competition);
    $this->assertEquals($dto->name, $competition->name);
}
```

### Feature Tests (HTTP Endpoints)
```php
public function test_user_can_create_order()
{
    $response = $this->postJson('/api/orders', [
        'reward_id' => 1,
        'quantity' => 2
    ]);

    $response->assertStatus(201);
    $this->assertDatabaseHas('orders', ['reward_id' => 1]);
}
```

### N+1 Prevention Tests
```php
public function test_orders_index_has_no_n_plus_one()
{
    Order::factory()->count(20)->create();

    DB::enableQueryLog();
    $this->orderRepository->index();
    $queries = DB::getQueryLog();

    $this->assertLessThan(10, count($queries));
}
```

---

## Conclusion

This architecture provides:
- **Separation of Concerns**: Each layer has a clear responsibility
- **Testability**: Easy to unit test individual components
- **Maintainability**: Changes are localized to specific layers
- **Scalability**: Can handle growing complexity
- **Performance**: N+1 queries eliminated through proper eager loading

Follow these patterns consistently across the application for a robust, maintainable codebase.
