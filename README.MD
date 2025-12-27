# 🐳 Dock-Hosting

**Dock-Hosting** is a modern Platform-as-a-Service (PaaS) that enables users to deploy PHP applications instantly with automatic subdomain provisioning and free SSL certificates.

 **Live Demo:** [https://www.dockhosting.dev](https://www.dockhosting.dev)

---

##  Key Features

### **Automatic Deployment**
- **Framework Selection:** Choose PHP (more frameworks coming soon)
- **Zero Configuration:** No manual setup required
- **Instant Deployment:** Projects go live in seconds

### **Smart Subdomain Management**
- **Vercel-Style Naming:** Auto-generated unique names (e.g., `my-app-a1b2c3.dockhosting.dev`)
- **Automatic DNS Routing:** Wildcard subdomain support via nginx-proxy
- **Free SSL:** Let's Encrypt certificates auto-generated for all projects

### **User Management**
- **Secure Authentication:** Password hashing with bcrypt
- **Personal Dashboard:** Real-time overview of deployments
- **Project Isolation:** Each project runs in its own Docker container

### **Built-in Code Editor**
- **File Manager:** Browse and edit project files directly in the browser
- **Live Updates:** Changes reflect immediately

---

##  Architecture

```
User Request → Dock-Hosting App → Docker Container (php:8.2-apache)
                      ↓
              nginx-proxy (Reverse Proxy)
                      ↓
        Auto SSL + Subdomain Routing
                      ↓
           https://project.dockhosting.dev
```

**Stack:**
- **Backend:** PHP 8.2 (OOP, PDO)
- **Database:** MySQL 8.0
- **Containers:** Docker + nginx-proxy
- **SSL:** Let's Encrypt (acme-companion)
- **Frontend:** TailwindCSS, Vanilla JavaScript

---

##  Production Deployment

### Prerequisites
- Ubuntu/Debian VPS with public IP
- Docker & Docker Compose installed
- Domain name with DNS access

### Quick Start

```bash
# Clone repository
git clone https://github.com/ilyas-doughmi/Dock-Hosting.git
cd Dock-Hosting

# Create environment file
cp .env.example .env
nano .env
```

**Edit `.env`:**
```ini
DB_PASSWORD=password123
STORAGE_PATH=/home/deployer/users
HOST_BASE_PATH=/home/deployer/users
LETSENCRYPT_EMAIL=your@email.com
```

**Setup:**
```bash
# Create storage directory
sudo mkdir -p /home/deployer/users/Projects
sudo chmod -R 777 /home/deployer/users

# Create proxy network
docker network create proxy_network

# Install nginx-proxy (reverse proxy + SSL)
docker run -d -p 80:80 -p 443:443 \
  --name nginx-proxy \
  --network proxy_network \
  -v /var/run/docker.sock:/tmp/docker.sock:ro \
  -v /etc/nginx/certs \
  -v /etc/nginx/vhost.d \
  -v /usr/share/nginx/html \
  nginxproxy/nginx-proxy

# Install SSL companion
docker run -d \
  --name nginx-proxy-acme \
  --network proxy_network \
  --volumes-from nginx-proxy \
  -v /var/run/docker.sock:/var/run/docker.sock:ro \
  -e DEFAULT_EMAIL=your@email.com \
  nginxproxy/acme-companion

# Start Dock-Hosting
docker compose up -d --build
```

### DNS Configuration

Add these records to your domain:

| Type | Name | Value |
|------|------|-------|
| A | @ | `YOUR_VPS_IP` |
| A | www | `YOUR_VPS_IP` |
| A | * | `YOUR_VPS_IP` |

The wildcard (`*`) enables automatic subdomains for user projects.

---

##  Local Development

```bash
# Requirements: Docker Desktop, Git

# Clone and setup
git clone https://github.com/ilyas-doughmi/Dock-Hosting.git
cd Dock-Hosting

# Create .env
echo "DB_PASSWORD=root" > .env

# Start services
docker compose up -d

# Access
# Main app: http://localhost
# Database: localhost:3306
```

---

##  Project Structure

```
Dock-Hosting/
├── Classes/          # OOP models (User, Project)
├── components/       # Reusable UI components
├── includes/         # Backend handlers (create-project, login)
├── pages/            # Frontend views (dashboard, file-manager)
├── php/              # Database connection
├── sql/              # Database schema
├── .env.example      # Environment template
├── docker-compose.yml
└── Dockerfile
```

---

##  How It Works

1. **User creates project** → Selects framework and enters name
2. **Backend generates** → Unique container name with random suffix
3. **Docker launches** → `php:8.2-apache` container with volume mount
4. **nginx-proxy detects** → Auto-configures subdomain routing
5. **Let's Encrypt** → Issues SSL certificate automatically
6. **Live in seconds** → `https://project-abc123.dockhosting.dev` 

---

##  Features Roadmap

- [x] PHP framework support
- [x] Automatic SSL certificates
- [x] Subdomain routing
- [x] File manager / Code editor

---

##  License

MIT License - Feel free to use for learning and personal projects.

---

**Built with ❤️ by [Ilyas Doughmi](https://github.com/ilyas-doughmi)**