# 💠 Clara
An MVC framework built with PHP 7.

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

## Usage
* Clara is mostly self-documenting.
* Config files are in ```src/setup/```.
* Core files are at ```src/core/```.
* Add controller and models in ```src/app/controllers``` and ```src/app/models``` respectively.

## Project Structure
Clara is built on the following structural architecture:
```bash
/clara
  /public                                   # Assets like images, CSS and JS files here
  /src                                      # Clara source code
    /app                                    # Dedicated directory for controllers and models
      /controllers                          # Add your controllers here
      /models                               # Add your models here
    /core                                   # Clara core classes
    /setup                                  # Dedicated directory for configuration
      config.php                            # Configure globals here
      routes.php                            # Configre routes here
  /vendor                                   # Composer files and 3rd party packages
  .htaccess                                 # Routes all traffic to index.php
  index.php                                 # Initializes app cycle
```

## Motivation
Clara was built with the purpose of understanding how major PHP frameworks operate under the hood. Most frameworks like Laravel implement techniques that can seem like "magic" unless you actually implement them yourself, an example being utilizing reflection API to plug in dependencies. Clara has helped me so much with familarizing myself with quite a few advanced concepts in the PHP & OOP world.
