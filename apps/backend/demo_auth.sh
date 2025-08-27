#!/bin/bash

# Demo Authentication Flow Script
# Make sure the Laravel server is running: php artisan serve

echo "🚀 Tasks & Tags API Authentication Demo"
echo "========================================"

# Base URL
BASE_URL="http://localhost:8000/api"

echo ""
echo "1️⃣ Health Check (Public Endpoint)"
echo "----------------------------------"
curl -s -X GET "$BASE_URL/health" | jq '.'

echo ""
echo ""
echo "2️⃣ Login with Demo User"
echo "------------------------"
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/auth/login" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "demo@local",
    "password": "demo1234"
  }')

echo "Login Response:"
echo "$LOGIN_RESPONSE" | jq '.'

# Extract token
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.access_token')

if [ "$TOKEN" = "null" ] || [ -z "$TOKEN" ]; then
    echo "❌ Login failed! Make sure the server is running and demo user exists."
    exit 1
fi

echo ""
echo "✅ Token extracted: ${TOKEN:0:20}..."

echo ""
echo ""
echo "3️⃣ Get User Info (Protected Endpoint)"
echo "--------------------------------------"
curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo ""
echo "4️⃣ Try to Access Protected Endpoint Without Token"
echo "--------------------------------------------------"
curl -s -X GET "$BASE_URL/auth/me" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo ""
echo "5️⃣ Logout (Invalidate Token)"
echo "-----------------------------"
curl -s -X POST "$BASE_URL/auth/logout" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json"

echo ""
echo "Logout completed (204 No Content)"

echo ""
echo ""
echo "6️⃣ Try to Access Protected Endpoint After Logout"
echo "------------------------------------------------"
curl -s -X GET "$BASE_URL/auth/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" | jq '.'

echo ""
echo ""
echo "✅ Demo completed!"
echo ""
echo "📝 Notes:"
echo "- Server must be running: php artisan serve"
echo "- Demo user must exist: php artisan db:seed"
echo "- Install jq for better JSON formatting: brew install jq (macOS) or apt-get install jq (Ubuntu)"
