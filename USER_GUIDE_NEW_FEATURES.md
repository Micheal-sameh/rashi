# Quick User Guide - New Features

## 1. Bonus/Penalty Approval System

### For Non-Admin Users
When you create a bonus or penalty:
1. Navigate to **Bonus/Penalties** → **Add Bonus/Penalty**
2. Fill in the required fields
3. Submit the form
4. Your bonus/penalty will be marked as "Pending Approval"
5. Wait for an admin to approve it
6. Points will NOT be applied until approved

### For Admin Users
When you create a bonus or penalty:
1. Navigate to **Bonus/Penalties** → **Add Bonus/Penalty**
2. Fill in the required fields
3. Submit the form
4. Your bonus/penalty is AUTOMATICALLY approved
5. Points are IMMEDIATELY applied to the user's account

To approve/reject pending items:
1. Click **Pending Approvals** in the sidebar (admin only)
2. Use search/filter to find specific items:
   - Search by user name or membership code
   - Filter by who created it
3. Review the details (user, groups, type, points, reason, creator)
4. Click **Approve** to apply the bonus/penalty and update points
5. Click **Reject** to delete the request

### Viewing Approved Bonuses/Penalties
1. Click **Bonus/Penalties** in the sidebar
2. You'll see only approved items with the "Approved By" column
3. Use search and filters:
   - Search by name or membership code
   - Filter by creator
   - Filter by specific user

## 2. Families Page

### Searching for Families
1. Click **Families** in the sidebar
2. Enter a name or membership code in the search box
3. Click **Search**
4. Results show all families matching the search
5. Each family card shows:
   - Family code (e.g., E1C1F001)
   - Number of members
   - List of all member names with their codes

### Viewing Family Details
1. From the families list, click **View Details** on any family
2. You'll see detailed information for EACH family member:
   - **Profile picture and basic info**
   - **Final Score**: Current quiz score
   - **Final Points**: Current point balance
   - **Quizzes Solved**: X out of Y quizzes completed
   - **Last Quiz**: Name and date of last quiz taken
   - **Last Redeem**: Last reward claimed and when
   - **Last Bonus**: Points and date of last bonus received
   - **Last Penalty**: Points and date of last penalty applied
   - **Last Competition**: Name and date of last competition participated
   - **Groups**: All groups except "General"

### Family Code Pattern
- Format: `E1C1FxxxNRy`
  - E1C1Fxxx = Family identifier
  - NRy = Member number (NR1, NR2, NR3, etc.)
- Example: E1C1F001NR1, E1C1F001NR2 are in the same family

## 3. User History

### Viewing User Point History
1. Click **User History** in the sidebar
2. Enter a user name or membership code
3. Click **Search**

### Understanding the History Display
Once a user is found, you'll see:

**Summary Cards:**
- **Current Points**: User's current point balance
- **Total Credit**: Sum of all points added
- **Total Debit**: Sum of all points removed
- **Net Balance**: Credit minus Debit (should match current points)

**Detailed History Table:**
- **Date**: When the transaction occurred
- **Type**: Credit (addition) or Debit (removal)
- **Source**: Where the points came from (Quiz, Bonus, Order, etc.)
- **Description**: Details about the transaction
- **Credit/Debit**: Amount added or removed
- **Balance**: Running balance after this transaction

### Transaction Sources
Points can come from:
- **Quiz**: Completing quizzes
- **Competition**: Participating in competitions
- **Bonus**: Admin-approved bonuses
- **Order**: Redeeming rewards (debit)
- **Penalty**: Admin-approved penalties (debit)
- **Welcome Bonus**: Initial sign-up bonus

## 4. Navigation Tips

### Sidebar Organization
The sidebar is organized by functionality:

**User Management:**
- Users
- Admin Users
- Leaderboard
- Families
- User History

**Content Management:**
- Competitions
- Quizzes
- Questions

**System Settings:**
- Settings
- Groups

**Points & Rewards:**
- Bonus/Penalties
- Pending Approvals (Admin only)
- Rewards
- Orders

**Communications:**
- Notifications
- About Us
- Terms
- Social Media
- Info Videos

## 5. Search Tips

### Name Search
- Searches are case-insensitive
- Partial matches work (e.g., "john" finds "John Smith")
- Works in both English and Arabic

### Membership Code Search
- Enter full or partial membership code
- E.g., "E1C1F001" finds all members of that family
- "NR1" finds all first members of families

### Filtering
- Filters can be combined with search
- Reset button clears all filters
- Filters persist during pagination

## 6. Mobile Usage

All new features are fully responsive:
- Tables become cards on mobile
- Touch-friendly buttons
- Swipe to scroll horizontal content
- Optimized for small screens

## 7. Permissions

| Feature | Regular User | Admin |
|---------|--------------|-------|
| Create Bonus/Penalty | Yes (Pending) | Yes (Auto-approved) |
| View Pending Approvals | No | Yes |
| Approve/Reject | No | Yes |
| View Families | Yes | Yes |
| View User History | Yes | Yes |
| View Own Bonuses | Yes | Yes |

## 8. Common Workflows

### Workflow 1: Approving a Bonus Request
1. Navigate to **Pending Approvals**
2. Find the request (use search/filter if needed)
3. Review the details carefully
4. Click **Approve**
5. User's points are updated immediately
6. Notification is sent to the user

### Workflow 2: Checking Family Progress
1. Navigate to **Families**
2. Search for family by code or member name
3. Click **View Details**
4. Review each member's statistics
5. Compare progress across family members
6. Identify who needs encouragement or rewards

### Workflow 3: Investigating Point Discrepancies
1. Navigate to **User History**
2. Search for the user
3. Review the complete transaction history
4. Check the running balance
5. Verify Total Credit - Total Debit = Current Points
6. Identify any unusual transactions
7. Contact admin if discrepancies found

## 9. Best Practices

### For Creating Bonuses/Penalties
- ✅ Provide clear, descriptive reasons
- ✅ Double-check the point amount
- ✅ Select the correct user
- ✅ Verify the type (Bonus vs Penalty)

### For Approving Requests
- ✅ Review the creator and reason
- ✅ Check if the amount is reasonable
- ✅ Verify the user hasn't already received similar bonus
- ✅ Consider checking user history before approving large amounts

### For Using Families Feature
- ✅ Use it to track family engagement
- ✅ Identify top-performing families
- ✅ Recognize families that need support
- ✅ Plan family-based competitions

### For User History
- ✅ Use it for point auditing
- ✅ Verify transaction accuracy
- ✅ Investigate user complaints
- ✅ Generate informal reports

## 10. Troubleshooting

**Problem: Can't see Pending Approvals in sidebar**
- Solution: This is admin-only. Contact an administrator.

**Problem: Bonus/Penalty not appearing**
- Solution: Check if you're viewing the correct page (approved vs pending)

**Problem: Family search returns no results**
- Solution: Verify membership code format (E1C1FxxxNRy)

**Problem: User history shows wrong balance**
- Solution: Report to admin - may need database reconciliation

**Problem: Can't find a specific user**
- Solution: Try searching by membership code instead of name

## 11. Support

For additional help:
- Contact system administrators
- Check the detailed implementation documentation
- Review user training materials
- Submit feedback through proper channels
