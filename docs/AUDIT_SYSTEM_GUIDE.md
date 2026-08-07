# Audit Log System - Implementation Guide

## Overview
A comprehensive audit logging system has been implemented to track all create, update, and delete operations across the dashboard. This system automatically logs all changes with detailed information about who made the change, when, and what was changed.

## Features
- ✅ Automatic tracking of create, update, and delete operations
- ✅ Records user information, IP address, and user agent
- ✅ Stores old and new values for all changes
- ✅ Sensitive data masking (passwords, tokens)
- ✅ Web interface for viewing audit logs
- ✅ Advanced filtering (action, model type, date range)
- ✅ Detailed view of individual audit entries
- ✅ Pagination support

## Database Structure

### audit_logs Table
- `id` - Primary key
- `action` - Type of action (created, updated, deleted)
- `model_type` - Full class name of the affected model
- `model_id` - ID of the affected record
- `user_id` - ID of user who performed the action
- `user_name` - Snapshot of user name
- `old_values` - JSON of previous values (for updates/deletes)
- `new_values` - JSON of new values (for creates/updates)
- `ip_address` - IP address of the request
- `user_agent` - Browser/client information
- `created_at` - Timestamp of the action
- `updated_at` - Standard Laravel timestamp

## Files Created

### 1. Migration
- `database/migrations/2026_01_21_003301_create_audit_logs_table.php`

### 2. Model
- `app/Models/AuditLog.php`

### 3. Trait
- `app/Traits/Auditable.php`

### 4. Controller
- `app/Http/Controllers/AuditLogController.php`

### 5. Views
- `resources/views/audit-logs/index.blade.php` - List view with filters
- `resources/views/audit-logs/show.blade.php` - Detailed view

### 6. Routes
Added to `routes/web.php`:
```php
Route::prefix('audit-logs')->group(function () {
    Route::get('/', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');
});
```

## Models Using Audit Trail

The following models have been updated to use the `Auditable` trait:

1. **Competition** - Tracks competition creation, updates, and deletions
2. **Quiz** - Tracks quiz management
3. **QuizQuestion** - Tracks question changes
4. **Reward** - Tracks reward modifications
5. **User** - Tracks user account changes
6. **Order** - Tracks order processing
7. **BonusPenalty** - Tracks bonus and penalty assignments

## Usage

### Adding Audit to New Models

To enable audit logging for any model, simply add the `Auditable` trait:

```php
use App\Traits\Auditable;

class YourModel extends Model
{
    use Auditable;

    // Your model code...
}
```

### Excluding Specific Fields

If you want to exclude certain fields from being audited:

```php
class YourModel extends Model
{
    use Auditable;

    protected $auditExclude = ['temporary_field', 'cache_data'];
}
```

### Viewing Audit Logs

**Web Interface:**
- Navigate to `/audit-logs` to view all audit logs
- Use filters to narrow down results:
  - Action type (created/updated/deleted)
  - Model type
  - Date range
- Click on any log entry to view detailed changes

**Programmatic Access:**
```php
// Get all audit logs for a specific model instance
$competition = Competition::find(1);
$auditLogs = $competition->auditLogs;

// Get all audit logs
$allLogs = AuditLog::with('user')->latest()->get();

// Filter by action
$creations = AuditLog::where('action', 'created')->get();

// Filter by model type
$userChanges = AuditLog::where('model_type', User::class)->get();
```

## Security Features

### Sensitive Data Protection
The system automatically masks sensitive fields:
- `password`
- `remember_token`
- `api_token`
- `secret`

These fields will be logged as `***REDACTED***` instead of their actual values.

### Who Can Access?
Currently, audit logs are accessible to users with the `admin` role (as defined in the route middleware). You can modify this in `routes/web.php` if needed.

## Translation Keys

Translation keys have been added to both English and Arabic language files:

### English (`resources/lang/en/messages.php`)
- `audit_logs` - "Audit Logs"
- `audit_log_details` - "Audit Log Details"
- `action` - "Action"
- `created` - "Created"
- `updated` - "Updated"
- `deleted` - "Deleted"
- And more...

### Arabic (`resources/lang/ar/messages.php`)
Corresponding Arabic translations have been added.

## What Gets Logged?

### On Create (INSERT)
- All fields and their initial values
- User who created the record
- Timestamp, IP, and user agent

### On Update (UPDATE)
- Only the fields that changed
- Old values and new values
- User who made the update
- Timestamp, IP, and user agent

### On Delete (DELETE)
- All field values before deletion
- User who deleted the record
- Timestamp, IP, and user agent

## Performance Considerations

1. **Async Processing**: For high-traffic applications, consider moving audit logging to a queue:
```php
// In the Auditable trait, modify auditLog method
dispatch(new LogAuditEvent($data));
```

2. **Log Retention**: Consider implementing a cleanup policy:
```php
// Delete logs older than 6 months
AuditLog::where('created_at', '<', now()->subMonths(6))->delete();
```

3. **Indexes**: The migration includes indexes on frequently queried columns:
   - `model_type` and `model_id`
   - `user_id`
   - `action`
   - `created_at`

## Example Queries

```php
// Find all changes by a specific user
$userChanges = AuditLog::where('user_id', 1)->get();

// Find all deletions in the last 7 days
$recentDeletions = AuditLog::where('action', 'deleted')
    ->where('created_at', '>=', now()->subDays(7))
    ->get();

// Find all changes to a specific competition
$competitionLogs = AuditLog::where('model_type', Competition::class)
    ->where('model_id', 1)
    ->orderBy('created_at', 'desc')
    ->get();

// Get audit trail with user information
$logsWithUsers = AuditLog::with('user')
    ->latest()
    ->paginate(50);
```

## Troubleshooting

### Audit logs not being created?
1. Check that the model uses the `Auditable` trait
2. Ensure migrations have been run: `php artisan migrate`
3. Check that the user is authenticated when making changes

### Performance issues?
1. Ensure database indexes are created
2. Consider implementing log rotation/archival
3. Use pagination when displaying logs

### Sensitive data being logged?
1. Add fields to the `$sensitiveFields` array in `Auditable` trait
2. Or use `$auditExclude` property in your model

## Future Enhancements

Consider adding:
1. **Export functionality** - Export audit logs to CSV/PDF
2. **Real-time notifications** - Alert admins of critical changes
3. **Rollback functionality** - Restore previous versions
4. **API endpoints** - Access audit logs via API
5. **Advanced analytics** - Visualize audit data with charts

## Maintenance

Run this artisan command to create a scheduled task for cleaning old logs:

```php
// In app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Clean audit logs older than 1 year
    $schedule->call(function () {
        AuditLog::where('created_at', '<', now()->subYear())->delete();
    })->monthly();
}
```
