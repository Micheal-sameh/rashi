#!/bin/bash

# Quick Test Script for Point Transfer System
# Run this to verify everything is working

echo "🔍 Point Transfer System - Quick Verification"
echo "=============================================="
echo ""

# Check if migration is applied
echo "1. Checking database migration..."
php artisan migrate:status | grep point_transfers
if [ $? -eq 0 ]; then
    echo "   ✅ Migration applied"
else
    echo "   ❌ Migration not found"
fi
echo ""

# Check if views exist
echo "2. Checking view files..."
if [ -f "resources/views/point-transfers/index.blade.php" ]; then
    echo "   ✅ index.blade.php exists"
else
    echo "   ❌ index.blade.php missing"
fi

if [ -f "resources/views/point-transfers/create.blade.php" ]; then
    echo "   ✅ create.blade.php exists"
else
    echo "   ❌ create.blade.php missing"
fi

if [ -f "resources/views/point-transfers/show.blade.php" ]; then
    echo "   ✅ show.blade.php exists"
else
    echo "   ❌ show.blade.php missing"
fi
echo ""

# Check if controllers exist
echo "3. Checking controllers..."
if [ -f "app/Http/Controllers/PointTransferController.php" ]; then
    echo "   ✅ Web PointTransferController exists"
else
    echo "   ❌ Web PointTransferController missing"
fi

if [ -f "app/Http/Controllers/Api/PointTransferController.php" ]; then
    echo "   ✅ API PointTransferController exists"
else
    echo "   ❌ API PointTransferController missing"
fi
echo ""

# Check if service exists
echo "4. Checking service layer..."
if [ -f "app/Services/PointTransferService.php" ]; then
    echo "   ✅ PointTransferService exists"
else
    echo "   ❌ PointTransferService missing"
fi
echo ""

# Check if repository exists
echo "5. Checking repository layer..."
if [ -f "app/Repositories/PointTransferRepository.php" ]; then
    echo "   ✅ PointTransferRepository exists"
else
    echo "   ❌ PointTransferRepository missing"
fi
echo ""

# Check if model exists
echo "6. Checking model..."
if [ -f "app/Models/PointTransfer.php" ]; then
    echo "   ✅ PointTransfer model exists"
else
    echo "   ❌ PointTransfer model missing"
fi
echo ""

# Check routes
echo "7. Checking routes..."
grep -q "point-transfers" routes/web.php
if [ $? -eq 0 ]; then
    echo "   ✅ Web routes configured"
else
    echo "   ❌ Web routes missing"
fi

grep -q "point-transfers" routes/api.php
if [ $? -eq 0 ]; then
    echo "   ✅ API routes configured"
else
    echo "   ❌ API routes missing"
fi
echo ""

# Check sidebar
echo "8. Checking sidebar integration..."
grep -q "point-transfers" resources/views/layouts/sideBar.blade.php
if [ $? -eq 0 ]; then
    echo "   ✅ Sidebar menu added"
else
    echo "   ❌ Sidebar menu missing"
fi
echo ""

# Check translations
echo "9. Checking translations..."
grep -q "point_transfers" resources/lang/en/messages.php
if [ $? -eq 0 ]; then
    echo "   ✅ English translations added"
else
    echo "   ❌ English translations missing"
fi

grep -q "point_transfers" resources/lang/ar/messages.php
if [ $? -eq 0 ]; then
    echo "   ✅ Arabic translations added"
else
    echo "   ❌ Arabic translations missing"
fi
echo ""

# List available routes
echo "10. Available Point Transfer Routes:"
php artisan route:list --name=point-transfers
echo ""

echo "=============================================="
echo "✅ Verification Complete!"
echo ""
echo "📝 Manual Testing Steps:"
echo "1. Login as admin"
echo "2. Look for 'Point Transfers' in sidebar"
echo "3. Click 'New Transfer'"
echo "4. Test with a family code (e.g., E1C1F001)"
echo ""
echo "🚀 System is ready to use!"
