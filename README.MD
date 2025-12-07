# 🐳 Dock-Hosting

**Dock-Hosting** is a lightweight "Platform as a Service" (PaaS) simulation that allows users to upload PHP projects and instantly deploy them to live Docker containers.

 **⚠️ Educational Project:** This application was built to demonstrate the interaction between **PHP**, **MySQL**, and **Docker System Commands**. It is a local simulation, not a production hosting environment.

---

## 🚀 Key Features

* **User Authentication:** Secure Signup & Login system with password hashing.
* **One-Click Deployment:** Upload a `.zip` file of your website, and the system handles the rest.
* **Automated Docker Orchestration:**
    * Unzips and isolates project files.
    * Calculates the next available port (e.g., `8001`, `8002`).
    * Spins up a dedicated Docker container (Apache/PHP 8.2).
* **Live Dashboard:** Real-time overview of active containers and system status.
* **Persistent Data:** All users and project metadata are stored in MySQL.

---

## 🛠️ Tech Stack

* **Backend:** PHP 8.x (Native/Vanilla)
* **Database:** MySQL / MariaDB
* **Infrastructure:** Docker & Docker Desktop
* **Frontend:** HTML5, TailwindCSS (CDN)
* **Server:** Apache (XAMPP/Localhost)

---

## ⚙️ Installation & Setup

### 1. Prerequisites
Ensure you have the following installed on your machine:
* [XAMPP](https://www.apachefriends.org/) (for PHP & MySQL).
* [Docker Desktop](https://www.docker.com/products/docker-desktop/) (must be running).

### 2. Database Setup
1.  Open **phpMyAdmin** (`http://localhost/phpmyadmin`).
2.  Create a new database named `dockhosting`.
3.  Import the `sql/install.sql` file provided in this repository to create the `users` and `Project` tables.

### 3. Project Configuration
1.  Clone or download this repo into your `htdocs` folder (e.g., `C:\xampp\htdocs\Dock-Hosting`).
2.  Open `php/connect.php` and check your database credentials (default is usually `root` / empty password).
3.  **Important:** Ensure your `storage` or `users` folder has write permissions so PHP can upload files.

### 4. Running the App
1.  Start **Apache** and **MySQL** in XAMPP.
2.  Start **Docker Desktop**.
3.  Navigate to `http://localhost/Dock-Hosting` in your browser.

---

## 📂 Project Structure

```text
Dock-Hosting/
├── Classes/          # OOP Logic (Login, Signup, Project)
├── components/       # Reusable UI parts (Sidebar, etc.)
├── includes/         # Form handlers (create-project.php, etc.)
├── pages/            # View files (dashboard.php, login.php)
├── php/              # Database connection
├── sql/              # Database schema
└── users/Projects/   # Storage for user uploads (Volume Mounts)