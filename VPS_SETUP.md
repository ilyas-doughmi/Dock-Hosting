# 🛡️ VPS Security & Setup Script

Run these commands on your fresh VPS (as root) to secure it and prepare it for Dock-Hosting.

## 1. System Update
First, make sure everything is up to date.
```bash
apt update && apt upgrade -y
```

## 2. Essential Tools
Install common utilities.
```bash
apt install -y curl git ufw fail2ban unzip htop
```

## 3. Firewall Setup (UFW)
This is critical. We deny everything incoming by default and only allow specific ports.
```bash
# default policies
ufw default deny incoming
ufw default allow outgoing

# Allow SSH (Important! Otherwise you lock yourself out)
ufw allow 22/tcp

# Allow HTTP/HTTPS (For your website)
ufw allow 80/tcp
ufw allow 443/tcp

# Allow Nginx Proxy Manager Admin Port
ufw allow 81/tcp

# Allow User Container Ports (Range 8000-9000)
ufw allow 8000:9000/tcp

# Enable the firewall
ufw enable
```
*Type `y` when it warns you about disrupting SSH connections.*

## 4. Install Docker & Docker Compose
The engine that powers everything.
```bash
curl -fsSL https://get.docker.com -o get-docker.sh
sh get-docker.sh
```

## 5. Create a Non-Root User (Best Practice)
Don't use `root` for everything. Let's create a user named `deployer`.
```bash
# Create user
adduser deployer

# Add to sudo group (admin rights)
usermod -aG sudo deployer

# Add to docker group (can run docker without sudo)
usermod -aG docker deployer
```

## 6. Fail2Ban (Brute Force Protection)
This bans IP addresses that try to guess your password too many times.
```bash
# It's already installed from step 2, just needs to be running.
systemctl start fail2ban
systemctl enable fail2ban
```

---

## 🚀 Next Steps (Login as new user)
Now log out and log back in as your new user to start deploying.
```bash
exit
ssh deployer@<YOUR_VPS_IP>
```
