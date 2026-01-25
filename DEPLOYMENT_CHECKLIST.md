# Deployment Checklist - Bonus/Penalty Approval & New Features

## Pre-Deployment Steps

### 1. Backup Database
- [ ] Create full database backup
- [ ] Test backup restoration on development server
- [ ] Document backup location and timestamp

### 2. Code Review
- [ ] Review all new controller methods
- [ ] Check model relationships
- [ ] Verify route definitions
- [ ] Review blade templates for syntax errors
- [ ] Check translation files for completeness

### 3. Test Environment Verification
- [ ] All new features tested in development
- [ ] Mobile responsive design verified
- [ ] Cross-browser testing completed (Chrome, Firefox, Safari, Edge)
- [ ] Arabic language translations verified
- [ ] Permission system tested (admin vs non-admin)

## Deployment Steps

### Step 1: Pull Code Changes
```bash
# Navigate to project directory
cd /path/to/rashi

# Pull latest changes
git pull origin main

# Or upload files via FTP if not using git
```

### Step 2: Install Dependencies (if needed)
```bash
# Update composer dependencies
composer install --no-dev --optimize-autoloader

# Clear and rebuild autoload files
composer dump-autoload
```

### Step 3: Run Database Migration
```bash
# Run the migration
php artisan migrate

# Verify migration was successful
php artisan migrate:status
```

### Step 4: Handle Existing Data
Choose one of these options for existing bonus/penalty records:

**Option A: Mark all as approved**
```sql
UPDATE bonuses_penalties
SET status = 2, approved_by = created_by
WHERE status = 1;
```

**Option B: Mark only admin-created as approved**
```sql
UPDATE bonuses_penalties bp
INNER JOIN model_has_roles mhr ON bp.created_by = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
SET bp.status = 2, bp.approved_by = bp.created_by
WHERE r.name = 'admin'
  AND mhr.model_type = 'App\\Models\\User'
  AND bp.status = 1;
```

**Option C: Leave all as pending for review**
- No SQL needed, but admin must review all existing records

### Step 5: Clear Cache
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Rebuild cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 6: Set Permissions (if on Linux/Unix)
```bash
# Set proper permissions
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## Post-Deployment Testing

### 1. Basic Functionality Tests
- [ ] Can login successfully
- [ ] Sidebar displays correctly
- [ ] All menu items are accessible

### 2. Bonus/Penalty Approval Tests
- [ ] **As Admin:**
  - [ ] Create bonus/penalty (should auto-approve)
  - [ ] Verify points are added immediately
  - [ ] View pending approvals page
  - [ ] Approve a pending item
  - [ ] Reject a pending item

- [ ] **As Non-Admin:**
  - [ ] Create bonus/penalty (should be pending)
  - [ ] Verify points are NOT added
  - [ ] Verify cannot see pending approvals menu item
  - [ ] Verify cannot access pending approvals URL directly

### 3. Families Feature Tests
- [ ] Search by membership code
- [ ] Search by user name
- [ ] View family list
- [ ] Click on family to view details
- [ ] Verify all statistics are displayed
- [ ] Check groups display (excluding General)
- [ ] Test mobile responsive view

### 4. User History Tests
- [ ] Search by user name
- [ ] Search by membership code
- [ ] Verify summary cards display correctly
- [ ] Check point history table
- [ ] Verify running balance calculations
- [ ] Test mobile responsive view

### 5. Translation Tests
- [ ] Switch to Arabic language
- [ ] Verify all new pages display Arabic correctly
- [ ] Check RTL layout works properly
- [ ] Verify English translations
- [ ] Check translation keys aren't showing

### 6. Performance Tests
- [ ] Check page load times
- [ ] Test with large datasets (if available)
- [ ] Verify pagination works
- [ ] Check database query efficiency

## Rollback Plan

If issues occur, follow these steps:

### 1. Revert Code
```bash
# If using git
git revert HEAD

# Or restore from backup
# cp -r /backup/path/* /project/path/
```

### 2. Rollback Database
```bash
# Run migration rollback
php artisan migrate:rollback

# Or restore from database backup
# mysql -u username -p database_name < backup.sql
```

### 3. Clear Cache Again
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Monitoring After Deployment

### First 24 Hours
- [ ] Monitor error logs: `storage/logs/laravel.log`
- [ ] Check database error log
- [ ] Monitor server resources (CPU, Memory)
- [ ] Watch for user-reported issues
- [ ] Verify scheduled tasks still running

### First Week
- [ ] Review pending approval usage statistics
- [ ] Check families feature usage
- [ ] Monitor user history access
- [ ] Gather user feedback
- [ ] Document any issues or feature requests

## User Communication

### Before Deployment
- [ ] Notify admins of new features
- [ ] Schedule brief maintenance window if needed
- [ ] Prepare user announcement

### After Deployment
- [ ] Send announcement about new features
- [ ] Share user guide document
- [ ] Offer training session for admins
- [ ] Set up support channel for questions

## Documentation Updates

- [ ] Update main README if needed
- [ ] Add new features to changelog
- [ ] Update API documentation (if applicable)
- [ ] Create admin training materials
- [ ] Update user manual

## Known Issues / Limitations

Document any known issues here:
1. _None at this time_

## Support Contacts

**Technical Issues:**
- Developer: [Your contact info]
- System Admin: [Admin contact info]

**User Issues:**
- Support Email: [Support email]
- Help Desk: [Help desk contact]

## Success Criteria

Deployment is successful when:
- [ ] All automated tests pass
- [ ] Manual testing completed without errors
- [ ] No critical bugs reported in first 24 hours
- [ ] Users can access all new features
- [ ] Admin approval workflow functioning correctly
- [ ] Performance metrics within acceptable range

## Sign-Off

**Deployed By:** _________________ **Date:** _________

**Verified By:** _________________ **Date:** _________

**Approved By:** _________________ **Date:** _________

## Notes

_Add any deployment-specific notes or observations here:_

---

**Deployment Status:** ⬜ Not Started | ⬜ In Progress | ⬜ Completed | ⬜ Rolled Back

**Last Updated:** 2026-01-25
