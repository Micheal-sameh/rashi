-- SQL Commands for Updating Existing Bonus/Penalty Records
-- Run these commands after migrating the database if you want to mark existing records as approved

-- Option 1: Mark ALL existing bonuses/penalties as APPLIED (approved)
-- Use this if you want to consider all existing records as already approved
UPDATE bonuses_penalties
SET status = 2,
    approved_by = created_by
WHERE status = 1;

-- Option 2: Mark existing bonuses/penalties created by admins as APPLIED
-- Use this if you want to auto-approve only those created by admins
UPDATE bonuses_penalties bp
INNER JOIN model_has_roles mhr ON bp.created_by = mhr.model_id
INNER JOIN roles r ON mhr.role_id = r.id
SET bp.status = 2,
    bp.approved_by = bp.created_by
WHERE r.name = 'admin'
  AND mhr.model_type = 'App\\Models\\User'
  AND bp.status = 1;

-- Option 3: Check current status distribution
-- Run this to see how many records are in each status
SELECT
    status,
    CASE
        WHEN status = 1 THEN 'Pending Approval'
        WHEN status = 2 THEN 'Applied'
        ELSE 'Unknown'
    END as status_name,
    COUNT(*) as count
FROM bonuses_penalties
GROUP BY status;

-- Option 4: View pending approvals with creator info
-- Useful for manual review before bulk updates
SELECT
    bp.id,
    u.name as user_name,
    c.name as creator_name,
    bp.type,
    bp.points,
    bp.reason,
    bp.created_at
FROM bonuses_penalties bp
JOIN users u ON bp.user_id = u.id
JOIN users c ON bp.created_by = c.id
WHERE bp.status = 1
ORDER BY bp.created_at DESC;

-- Option 5: Count pending approvals by creator
-- See which users have pending items
SELECT
    c.name as creator_name,
    COUNT(*) as pending_count
FROM bonuses_penalties bp
JOIN users c ON bp.created_by = c.id
WHERE bp.status = 1
GROUP BY bp.created_by, c.name
ORDER BY pending_count DESC;
