# MyEduConnect - Vulnerable Education Platform

> **⚠️ EDUCATIONAL PURPOSE ONLY**  
> This platform contains **deliberate security vulnerabilities** for cybersecurity testing and education.  
> **DO NOT deploy in production or use with real data.**

---

## 📋 Overview

MyEduConnect is a mid-sized Malaysian education technology platform with intentional security flaws designed for:
- Security testing and auditing
- Vulnerability assessment practice
- OWASP Top 10 demonstration
- Penetration testing training

---

## 🚀 Quick Start (5 Minutes)

### Prerequisites

| Requirement | Version |
|-------------|---------|
| Docker Desktop | 20.10+ |
| Git | (optional) |
| RAM | 4GB minimum |
| Disk Space | 5GB |

### Installation Steps

#### Step 1: Clone or Download

**Option A - Clone with Git:**
```bash
git clone https://github.com/Qistina443/MyEduConnect.git
cd MyEduConnect

Option B - Download ZIP:

Click "Code" → "Download ZIP"

Extract the ZIP file

Open terminal in extracted folder

Step 2: Start Docker Desktop
Ensure Docker Desktop is running (whale icon in system tray)


Step 3: Build and Run
docker compose up -d

Main Application: http://localhost:8080
phpMyAdmin: http://localhost:8081
REST API: http://localhost:8080/api/index.php

Step 5: Test Login
Role	Email	                   Password
Admin	admin@myeduconnect.com	  admin123
Student	ali@myeduconnect.com	   password123
Student	jane@myeduconnect.com	     password123
Instructor	emily@myeduconnect.com	teacher123
 
