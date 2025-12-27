# 🚀 Production Deployment Guide: Dock-Hosting.dev

This guide details how to host **Dock-Hosting** on a VPS (Ubuntu) using **Docker** and **Nginx Proxy Manager**. This setup allows you to run your main platform on `https://dockhosting.dev` while hosting multiple other projects or user containers on the same server securely.

---

## 🏗️ Architecture Overview

1.  **VPS (Ubuntu)**: The raw server hardware (Contabo).
2.  **Docker**: The engine running all services.
3.  **Nginx Proxy Manager (NPM)**: The "Traffic Cop". It listens on ports 80/443.
    *   Traffic for `dockhosting.dev` -> forwards to **Dock-Hosting App**.
    *   Traffic for `client.com` -> forwards to **Client Container**.
4.  **Dock-Hosting App**: Your PHP application. It has access to the VPS's Docker socket to create/delete containers for your users.

---

## 🛠️ Step 1: Server Setup

Connect to your VPS via SSH:
```bash
ssh root@<YOUR_VPS_IP>
```

### 1. Update & Security
```bash
apt update && apt upgrade -y
```

### 2. Install Docker & Docker Compose
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
```

---

## 🌐 Step 2: Set up the Reverse Proxy

We will create a specific network for public access so containers can talk to each other.

1.  **Create the network**:
    ```bash
    docker network create proxy_network
    ```

2.  **Set up Nginx Proxy Manager**:
    Create a folder:
    ```bash
    mkdir -p /root/proxy
    cd /root/proxy
    nano docker-compose.yml
    ```

3.  **Paste this configuration**:
    ```yaml
    version: '3.8'
    services:
      app:
        image: 'jc21/nginx-proxy-manager:latest'
        restart: unless-stopped
        ports:
          - '80:80'      # Public HTTP
          - '81:81'      # Admin UI (Access via IP:81)
          - '443:443'    # Public HTTPS
        volumes:
          - ./data:/data
          - ./letsencrypt:/etc/letsencrypt
        networks:
          - proxy_network

    networks:
      proxy_network:
        external: true
    ```

4.  **Start it**:
    ```bash
    docker compose up -d
    ```

---

## 📦 Step 3: Deploy Dock-Hosting

Now we deploy your actual application code.

### 1. Upload Code
You can use **FileZilla** (SFTP) or `git clone` to put your project in `/var/www/dock-hosting`.

### 2. Create Production `docker-compose.yml`
Inside your project folder (`/var/www/dock-hosting`/):

```yaml
version: '3.8'

services:
  web:
    build: .
    container_name: dock-hosting-app
    restart: always
    volumes:
      # Mount your code
      - ./:/var/www/html
      # ⚠️ CRITICAL: Give app access to control Host Docker
      - /var/run/docker.sock:/var/run/docker.sock
      # Persist user project data
      - ./users/Projects:/var/www/html/users/Projects
    networks:
      - proxy_network
      - default

networks:
  proxy_network:
    external: true
```

### 3. Create `Dockerfile` (If not exists)
```dockerfile
FROM php:8.2-apache

# Install system dependencies for Docker control inside PHP
RUN apt-get update && apt-get install -y \
    docker.io \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache Mod Rewrite
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html
```

### 4. Start the Application
```bash
docker compose up -d --build
```

---

## 🔗 Step 4: Connect Domain (Cloudflare/Registrar)

1.  Go to your Domain Provider (Namecheap, GoDaddy, etc.).
2.  Create an **A Record**:
    *   **Name**: `@` (or `dockhosting.dev`)
    *   **Value**: `<YOUR_VPS_IP>`
3.  Create a CNAME (Optional):
    *   **Name**: `www`
    *   **Value**: `dockhosting.dev`

---

## 🚦 Step 5: Configure Nginx Proxy Manager

1.  Go to `http://<YOUR_VPS_IP>:81` in your browser.
2.  Login: `admin@example.com` / `changeme` (Update these immediately!).
3.  Click **"Proxy Hosts"** -> **"Add Proxy Host"**.

### Configuration:
*   **Domain Names**: `dockhosting.dev` (and `www.dockhosting.dev`)
*   **Scheme**: `http`
*   **Forward Hostname / IP**: `dock-hosting-app` (This matches `container_name` in Step 3)
*   **Forward Port**: `80`
*   **Cache Assets**: Enable
*   **Block Common Exploits**: Enable

### SSL Tab (The Padlock 🔒):
*   **SSL Certificate**: "Request a new SSL Certificate"
*   **Force SSL**: Enable
*   **HTTP/2 Support**: Enable
*   **Email**: Your email address.
*   **Agree to Terms**: Check.

Click **Save**.

---

## ✅ Result

1.  Visit **`https://dockhosting.dev`**.
2.  You should see your **Dark Mode Homepage**.
3.  Log in and try creating a project.
    *   When the PHP app executes `docker run ...`, it talks to the VPS via the socket we mounted (`/var/run/docker.sock`).
    *   The container spawns on the VPS!

### Handling User Ports
Since users create containers on ports (e.g., `8888`), you have two options:

1.  **Open Firewall (Simple)**: Allow ports `8000-9000` on Ubuntu (`ufw allow 8000:9000/tcp`).
    *   User Link: `http://dockhosting.dev:8001`
2.  **Subdomains (Advanced)**: Use Nginx Proxy Manager API or Traefik to automate subdomain creation (e.g., `project1.dockhosting.dev`), but this requires advanced coding.
