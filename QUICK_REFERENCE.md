# Quick Reference: N+1 Prevention & Clean Code

## 🚫 Common N+1 Anti-Patterns

### ❌ DON'T: Load relationships in loops
```php
// BAD - N+1 Query
$orders = Order::all();
foreach ($orders as $order) {
    echo $order->user->name;  // Extra query!
    echo $order->reward->name; // Extra query!
}
```

### ✅ DO: Eager load relationships
```php
// GOOD - 1 Query
$orders = Order::with(['user', 'reward'])->get();
foreach ($orders as $order) {
    echo $order->user->name;
    echo $order->reward->name;
}
```

---

## 🎯 Layer Responsibilities

| Layer | Responsibility | Example |
|-------|---------------|---------|
| **Controller** | HTTP handling | `return view('orders.index', compact('orders'));` |
| **Service** | Business logic | `DB::transaction(function() { ... });` |
| **Repository** | Data access | `return $this->model->with(['user'])->get();` |
| **Model** | Entity definition | `public function user() { return $this->belongsTo(User::class); }` |

---

## 🔧 Quick Fixes Reference

### When returning collections
```php
// ❌ DON'T
$users = User::all();
return $users; // Missing relationships

// ✅ DO
$users = User::with(['groups', 'media'])->get();
return $users;
```

### When using scopes
```php
// ❌ DON'T
Order::with(['user', 'reward', 'servant'])->get();

// ✅ DO
Order::withApiRelations()->get();
```

### When deleting related records
```php
// ❌ DON'T
foreach ($quiz->questions as $question) {
    foreach ($question->answers as $answer) {
        $answer->delete();
    }
    $question->delete();
}

// ✅ DO
$questionIds = $quiz->questions->pluck('id');
QuestionAnswer::whereIn('quiz_question_id', $questionIds)->delete();
QuizQuestion::whereIn('id', $questionIds)->delete();
```

---

## 📋 Code Checklist

### Before Committing
- [ ] Eager loaded all accessed relationships?
- [ ] Used query scopes instead of repeating `with()`?
- [ ] Kept controller thin (3-5 lines per method)?
- [ ] Business logic in service, not controller?
- [ ] Query building in repository, not service?
- [ ] Used bulk operations instead of loops?
- [ ] Added type hints to all methods?
- [ ] Tested query count (< 10 queries per endpoint)?

---

## 🎨 Model Query Scopes

### Competition
```php
Competition::withApiRelations()->get();
Competition::withFullData()->find($id);
```

### Order
```php
Order::withApiRelations()->get();
```

### Quiz
```php
Quiz::withFullData()->get();
```

---

## 🛠️ Debugging N+1

### Using QueryOptimizer
```php
use App\Helpers\QueryOptimizer;

// Analyze queries
$stats = QueryOptimizer::analyzeQueries(function () {
    return $this->orderService->index();
});

dd($stats); // Shows query_count, execution_time_ms, memory_used_mb
```

### Using Laravel Debugbar
```php
// Enable in .env
DEBUGBAR_ENABLED=true

// Check query count in bottom bar
```

### Manual Query Logging
```php
DB::enableQueryLog();

$orders = $this->orderRepository->index();

$queries = DB::getQueryLog();
dd(count($queries), $queries);
```

---

## 📊 Performance Targets

| Endpoint | Max Queries | Max Time |
|----------|------------|----------|
| List (paginated) | 5-10 | 100ms |
| Show (single) | 3-5 | 50ms |
| Create | 2-5 | 100ms |
| Update | 3-5 | 100ms |
| Delete | 2-3 | 50ms |

---

## 🎯 Common Patterns

### Pattern 1: List with Filters
```php
// Repository
public function index($filters = [])
{
    return $this->model->query()
        ->withApiRelations()
        ->when($filters['status'] ?? null, fn($q, $status) =>
            $q->where('status', $status)
        )
        ->paginate();
}
```

### Pattern 2: Show with All Relations
```php
// Repository
public function show($id)
{
    return $this->model->query()
        ->withFullData()
        ->findOrFail($id);
}
```

### Pattern 3: Dropdown Optimization
```php
// Repository
public function dropdown()
{
    return $this->model->query()
        ->select('id', 'name')
        ->orderBy('name')
        ->get();
}
```

### Pattern 4: Bulk Operations
```php
// Service
public function bulkDelete(array $ids)
{
    DB::transaction(function () use ($ids) {
        $this->repository->bulkDelete($ids);
        event(new BulkDeleted($ids));
    });
}

// Repository
public function bulkDelete(array $ids)
{
    return $this->model->whereIn('id', $ids)->delete();
}
```

---

## 🔍 Testing Examples

### Test N+1 Prevention
```php
public function test_prevents_n_plus_one()
{
    Order::factory()->count(20)->create();

    $queryCount = 0;
    DB::listen(function() use (&$queryCount) {
        $queryCount++;
    });

    $this->get('/orders');

    $this->assertLessThan(10, $queryCount);
}
```

### Test Repository
```php
public function test_repository_eager_loads()
{
    $order = Order::factory()->create();

    $result = $this->orderRepository->show($order->id);

    $this->assertTrue($result->relationLoaded('user'));
    $this->assertTrue($result->relationLoaded('reward'));
}
```

---

## 💡 Pro Tips

### 1. Use `loadMissing()` for conditional loading
```php
$user->loadMissing('groups'); // Only loads if not already loaded
```

### 2. Select specific columns in relationships
```php
->with('user:id,name,email') // Only these columns
```

### 3. Count relationships without loading
```php
$users = User::withCount('orders')->get();
// Access: $user->orders_count (no loading!)
```

### 4. Check if relation is loaded
```php
if ($user->relationLoaded('groups')) {
    // Use cached relation
} else {
    $user->load('groups');
}
```

### 5. Lazy eager loading for collections
```php
$users = User::all();
$users->load(['groups', 'media']); // Load for all at once
```

---

## 🚀 Quick Commands

### Check queries in Tinker
```bash
php artisan tinker
> DB::enableQueryLog();
> Order::with(['user', 'reward'])->get();
> DB::getQueryLog();
```

### Monitor in real-time (Telescope)
```bash
php artisan telescope:install
php artisan migrate
# Visit: /telescope
```

### Profile specific endpoint
```bash
php artisan route:list
# Add breakpoint with Debugbar enabled
```

---

## 📚 Related Documentation

- [N+1_REFACTORING_GUIDE.md](./N+1_REFACTORING_GUIDE.md) - Complete refactoring details
- [CLEAN_ARCHITECTURE_GUIDE.md](./CLEAN_ARCHITECTURE_GUIDE.md) - Architecture principles
- [CODE_REVIEW_SUMMARY.md](./CODE_REVIEW_SUMMARY.md) - Summary of changes

---

## 🆘 When in Doubt

1. **Is this N+1?** → Enable query log and check count
2. **Where should this logic go?** → Follow layer responsibilities
3. **How to optimize?** → Use QueryOptimizer helper
4. **Need help?** → Check documentation or ask team

---

**Remember**: Prevention is better than optimization. Always eager load relationships when you know you'll need them!
