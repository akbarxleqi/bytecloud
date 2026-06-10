#!/bin/bash

# Bytecloud Telegram Drive Installer
# A premium, interactive terminal-based installer

# Color definitions
RED='\033[0;31m'
GREEN='\033[0;32m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
YELLOW='\033[1;33m'
BOLD='\033[1m'
NC='\033[0m' # No Color

clear

# Banner
echo -e "${CYAN}${BOLD}====================================================${NC}"
echo -e "${BLUE}${BOLD}        ____        _            _                 _${NC}"
echo -e "${BLUE}${BOLD}       | __ ) _   _| |_ ___  ___| | ___  _   _  __| |${NC}"
echo -e "${BLUE}${BOLD}       |  _ \| | | | __/ _ \/ __| |/ _ \| | | |/ _\` |${NC}"
echo -e "${BLUE}${BOLD}       | |_) | |_| | ||  __/ (__| | (_) | |_| | (_| |${NC}"
echo -e "${BLUE}${BOLD}       |____/ \__, |\__\___|\___|_|\___/ \__,_|\__,_|${NC}"
echo -e "${BLUE}${BOLD}              |___/                                 ${NC}"
echo -e "${CYAN}${BOLD}====================================================${NC}"
echo -e "${YELLOW}           Telegram Cloud Storage Drive Installer${NC}"
echo -e "${CYAN}${BOLD}====================================================${NC}\n"

# Helper functions
status_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

status_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

status_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

status_error() {
    echo -e "${RED}[ERROR]${NC} $1"
    exit 1
}

# 1. System Requirement Checks
status_info "Checking system requirements..."

# Check PHP
if ! command -v php &> /dev/null; then
    status_error "PHP is not installed. Please install PHP 8.2 or higher."
else
    PHP_VERSION=$(php -r 'echo PHP_VERSION;')
    status_success "PHP $PHP_VERSION detected."
fi

# Check Composer
if ! command -v composer &> /dev/null; then
    status_error "Composer is not installed. Please install Composer."
else
    status_success "Composer detected."
fi

# Check Node.js
if ! command -v node &> /dev/null; then
    status_warning "Node.js is not installed. Asset compilation (Vite) will be skipped."
else
    status_success "Node.js detected."
fi

# Check NPM
if ! command -v npm &> /dev/null; then
    status_warning "NPM is not installed. Asset compilation (Vite) will be skipped."
else
    status_success "NPM detected."
fi

echo ""

# 2. Environment Setup (.env)
if [ ! -f .env ]; then
    status_info "Creating .env file from template..."
    cp .env.example .env
    status_success ".env file created."
else
    status_warning ".env file already exists."
    read -p "Do you want to overwrite it? (y/N): " OVERWRITE_ENV
    if [[ "$OVERWRITE_ENV" =~ ^[Yy]$ ]]; then
        status_info "Overwriting .env file..."
        cp .env.example .env
        status_success ".env file overwritten."
    fi
fi

# 3. Interactive Database Setup
read -p "Configure database connection now? (Y/n): " CONF_DB
CONF_DB=${CONF_DB:-"Y"}
if [[ "$CONF_DB" =~ ^[Yy]$ ]]; then
    read -p "Database Host [127.0.0.1]: " DB_HOST
    DB_HOST=${DB_HOST:-"127.0.0.1"}

    read -p "Database Port [3306]: " DB_PORT
    DB_PORT=${DB_PORT:-"3306"}

    read -p "Database Name [bytecloud]: " DB_DATABASE
    DB_DATABASE=${DB_DATABASE:-"bytecloud"}

    read -p "Database Username [root]: " DB_USERNAME
    DB_USERNAME=${DB_USERNAME:-"root"}

    read -s -p "Database Password: " DB_PASSWORD
    echo ""

    status_info "Writing database settings to .env..."

    # Use PHP to safely write changes to .env (cross-platform compatible)
    php -r "
        \$env = file_get_contents('.env');
        \$env = preg_replace('/^DB_HOST=.*$/m', 'DB_HOST=' . '$DB_HOST', \$env);
        \$env = preg_replace('/^DB_PORT=.*$/m', 'DB_PORT=' . '$DB_PORT', \$env);
        \$env = preg_replace('/^DB_DATABASE=.*$/m', 'DB_DATABASE=' . '$DB_DATABASE', \$env);
        \$env = preg_replace('/^DB_USERNAME=.*$/m', 'DB_USERNAME=' . '$DB_USERNAME', \$env);
        \$env = preg_replace('/^DB_PASSWORD=.*$/m', 'DB_PASSWORD=\"' . addslashes('$DB_PASSWORD') . '\"', \$env);
        file_put_contents('.env', \$env);
    "
    status_success "Database settings updated successfully."
fi

echo ""

# 4. Dependency Installation
status_info "Installing Composer dependencies..."
composer install --no-interaction --prefer-dist --optimize-autoloader
if [ $? -ne 0 ]; then
    status_error "Composer installation failed."
fi
status_success "Composer dependencies installed successfully."

# 5. Application Key
if grep -q "APP_KEY=base64" .env && [ -n "$(grep -E "^APP_KEY=base64:.+" .env)" ]; then
    status_info "Application key already exists."
else
    status_info "Generating Application Key..."
    php artisan key:generate --force
    status_success "Application key generated."
fi

# 6. Database Migrations
status_info "Running database migrations..."
php artisan migrate --force
if [ $? -ne 0 ]; then
    status_error "Database migration failed. Please ensure the database connection works and the database exists."
fi
status_success "Database migrated successfully."

# 7. Storage Symbolic Link
status_info "Linking public storage folder..."
php artisan storage:link
status_success "Storage folder linked."

# 8. Asset Compilation
if command -v npm &> /dev/null && command -v node &> /dev/null; then
    status_info "Installing NPM dependencies..."
    npm install
    status_info "Building production assets with Vite..."
    npm run build
    status_success "Frontend assets compiled successfully."
else
    status_warning "Skipping asset compilation as Node.js/NPM is missing."
fi

echo ""

# 9. Set Permissions
status_info "Setting directory permissions..."
chmod -R 775 storage bootstrap/cache
status_success "Permissions set."

echo -e "\n${GREEN}${BOLD}====================================================${NC}"
echo -e "${GREEN}${BOLD}            INSTALLATION COMPLETE!                  ${NC}"
echo -e "${GREEN}${BOLD}====================================================${NC}\n"
echo -e "You can now run ${CYAN}php artisan serve${NC} or point your webserver to the ${BOLD}public/${NC} directory.\n"
echo -e "${YELLOW}${BOLD}Important Next Steps:${NC}"
echo -e "1. ${BOLD}Background Queue Worker:${NC} Set up a background process manager (like systemd or supervisor) to run:"
echo -e "   ${CYAN}php artisan queue:work --tries=3${NC}"
echo -e "   This is required to handle upload queue processes to Telegram."
echo -e "2. ${BOLD}HTTPS Configuration:${NC} If deploying on a production server, make sure to configure SSL certificates (Let's Encrypt)."
echo -e "\nThank you for using Bytecloud!\n"
