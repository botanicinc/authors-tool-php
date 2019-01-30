# Botanic Author's Tool (BAT)

GUI for conversational graph editing/testing and Bot configuration

# Disclaimer
These files are made available to you on an as-is and restricted basis, and may only be redistributed or sold to any third party as expressly indicated in the Terms of Use for Seed Vault. Seed Vault Code (c) Botanic Technologies, Inc. Used under license.

# Build Development Setup 

## Backend PHP code

Create backend/.env file (it's excluded from the git repo). Use .env-example as source.\
make sure APP_ENV=local

```bash
# Docroot is at backend/public. You can make it work just by running cli php (or your webserver of choice)
# Make sure to listen to port 8000 in dev instance as frontend webpack will send api requests to it
apt-get update && apt-get install php7.0 php7.0-curl php7.0-json php7.0-mbstring php7.0-mcrypt php7.0-mysql php-apcu 
cd backend
composer install
vim .env
php -S localhost:8000 -t public
```


## Frontend 

Frontend is bundled with Webpack. It has it's own webserver to provide features like Hot-loading.
This builds and run the application as a development instance and sets the test Hadron web channel's URI

```bash
# To make webpack build dev files and start webpack webserver. 
# This will start listening by default to http://localhost:8080
cd frontend
npm ci
BOTANIC_ENV=local HADRON_URI="https://domain/dev_author/hadron.php" npm run dev
```
