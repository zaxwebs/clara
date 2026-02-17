# 💠 Clara
An MVC framework built with modern PHP 8.

## Requirements
- PHP 8.3+
- Composer 2+

## Installation
Install a local copy with the instructions below.

### Install LAMP stack
It is assumed you already know how to install a LAMP stack. I recommend doing so with Laragon as it greatly simplifies the task.
Laragon is a portable, isolated, fast & powerful universal development environment for PHP (with MySQL).
You can download it here: https://laragon.org/download/

### Install Composer
Installation instructions: https://getcomposer.org/download/

### Setup Server
1. Create a dedicated directory (and configure) for hosting Clara files.
2. Clone or copy Clara files to the directory.
3. Run `composer install`.

## Usage
- Config files are in `src/setup/`.
- Core files are in `src/core/`.
- Add controllers and models in `src/app/controllers` and `src/app/models` respectively.

## Project Structure
```bash
/clara
  /public
  /src
    /app
      /controllers
      /models
    /core
    /setup
      config.php
      routes.php
  /vendor
  .htaccess
  index.php
```
