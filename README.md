# OZUG - Offener Zugang zum Grundgesetz
Publishing platform for legal commentaries.

## How to Set Up

### Prerequisites
- Install Docker Desktop

### Run the Application Locally
- Clone this repository: `git clone git@github.com:blanq-agency/ozug.git`
- `cd` into the project directory: `cd ozug`
- `cd` into the webapp directory: `cd sources/webapp`
- Copy the `.env.example` file and name it `.env`, update it with application-specific configuration variables.
- Run the following command to install Laravel Sail: 
  ```
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
  ```
- Start the application using Docker with Laravel Sail: `sail up`  
  (use `./vendor/bin/sail` to execute Sail commands or [configure a shell alias](https://laravel.com/docs/10.x/sail#configuring-a-shell-alias))
- Install dependencies with _composer_: `sail composer install`
- Install frontend dependencies with _npm_: `sail npm install`
- Generate frontend assets: `sail npm run dev`
- Go to http://localhost:8001 and click on "GENERATE APP KEY" (the key will be stored in your `.env` file).

---
## Statamic Data
By default, Statamic stores the application configuration and data in several places.
It uses flat files (YAML and Markdown) for both the configuration (e.g. "blueprints" that define the model of a content type) and the data (e.g. an item of a certain type).

In order to separate the application source code and the user-generated content, this application stores the following data in a dedicated folder under `sources/webapp/data`:

- content
- revisions
- resources
- storage
- revisions
- users

The content of this folder is not tracked in this repository.
In order to retrieve the application data from the production server (or staging server), developers are expected to use the dedicated repository where this data is stored.
The repository where the data is: https://github.com/blanq-agency/ozug-data.
Access to this repository is restricted to users with appropriate permissions.

- Clone the `ozug-data` repository into `sources/webapp/data`. From `sources/webapp`:  
  `git clone git@github.com:blanq-agency/ozug-data.git data`
- If creating a project from scratch use the config files in the `data.example` directory:  
  `cp data.example data`  
  and create a new user:  
  `sail php please make:user`

The blueprints and users roles definitions are stored in the default location (`resources`), and are tracked in this repository.

- Run `sail artisan storage:link` to create a symbolic link to the storage folder

## Local Development
- `cd` into the webapp directory : `cd sources/webapp` and run `composer`, `npm` and `artisan` commands via Sail.
  See examples below.
- Depending on your use case, clone the remote git repository containing the application data (https://github.com/blanq-agency/ozug-data) under `sources/webapp/data`.
⚠️ The application data should always be pulled from the production/staging server, not the other way around.

### Pull the latest data from the production server
- `cd sources/webapp/data`
- `git pull`
- Log in to the CP and clear all caches under "Utilities > Cache Manager"

### Watching Javascript and CSS changes
- Watch local file changes (JS, CSS): `sail npm run watch`

### Running npm Commands
- `sail npm <command>`, e.g. `sail npm install`

### Running artisan Commands
- `sail artisan <command>`, e.g. `sail artisan config:clear`

### Running composer Commands
- `sail composer require <dependency>`, e.g. `sail composer require statamic/cms`

### Statamic Control Panel
- http://localhost:8001/cp

### Mailhog Dashboard
- To test sending and receiving mails from the application locally, go to http://localhost:8026

## Provision or deploy the production server
- The master branch is manually deployed to production using Ploi (ploi.io). Note: to enable auto deploy, go to Github > Settings > Webhooks and click "Active" on the Ploi webhook. Please leave disabled unless you know what you are doing.
