#!/bin/bash

# WARNING: This script will WIPE ALL DATA, including the database and user projects.
# Use for a fresh start only.

echo "⚠️  WARNING: This will DELETE ALL CONTAINERS, VOLUMES (Database), and PROJECT FILES."
echo "You have 5 seconds to cancel (Ctrl+C)..."
sleep 5

echo "🛑 Stopping all containers..."
docker stop $(docker ps -aq) 2>/dev/null

echo "🗑️  Removing all containers..."
docker rm $(docker ps -aq) 2>/dev/null

echo "🧹 Pruning unused volumes (Wipes Database)..."
docker volume prune -f

echo "🌐 Pruning unused networks..."
docker network prune -f

echo "🌐 Creating proxy_network..."
docker network create proxy_network 2>/dev/null || true

echo "📂 Wiping User Project Data..."
# Be very careful with rm -rf. We only want to delete the contents of Projects.
# Adjust this path if your projects are stored elsewhere.
PROJECTS_DIR="/home/deployer/dock-hosting-data/Projects"
if [ -d "$PROJECTS_DIR" ]; then
    rm -rf "$PROJECTS_DIR"/*
    echo "   ✅ Projects directory cleared."
else
    echo "   ⚠️  Projects directory not found at $PROJECTS_DIR"
fi

echo "🚀 Restarting Platform..."
# Try 'docker compose' (v2) first, then 'docker-compose' (v1)
if docker compose version >/dev/null 2>&1; then
    CMD="docker compose"
else
    CMD="docker-compose"
fi

$CMD down 2>/dev/null

echo "   🌐 Starting Nginx Proxy..."
if [ -d "proxy" ]; then
    cd proxy
    $CMD up -d --build
    cd ..
else
    echo "   ⚠️  'proxy' directory not found! Skipping proxy start."
fi

echo "   🚀 Starting Main Application..."
$CMD up -d --build

echo "✅ System Reset Complete! Database and User Projects are empty."
