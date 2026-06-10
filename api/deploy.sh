#!/bin/bash
echo "========================================"
echo "MyEduConnect Docker Deployment"
echo "========================================"
echo ""

echo "[1/5] Stopping any existing containers..."
docker-compose down

echo ""
echo "[2/5] Building Docker images..."
docker-compose build

echo ""
echo "[3/5] Starting containers..."
docker-compose up -d

echo ""
echo "[4/5] Waiting for database to initialize..."
sleep 10

echo ""
echo "[5/5] Checking container status..."
docker-compose ps

echo ""
echo "========================================"
echo "DEPLOYMENT COMPLETE!"
echo "========================================"
echo ""
echo "Access your application:"
echo "  Web App:      http://localhost:8080"
echo "  phpMyAdmin:   http://localhost:8081"
echo "  Main Page: http://localhost:8080/index.php"
echo "  API:          http://localhost:8080/api/index.php"
echo ""
echo "To stop: docker-compose down"
echo "========================================"