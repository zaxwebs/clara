💠 **Clara**
*A modern MVC framework built with PHP 8*

---

## Philosophy

Clara was born from curiosity.

It started as a personal exploration. Not to compete with established frameworks, but to understand them. To look beneath the abstractions of modern PHP development and study how an MVC framework truly works under the hood.

Every route, controller call, and model operation is written in plain, readable code you can follow step by step. The aim was to keep the framework simple and understandable.

Clara values:

* **Transparency over magic**
  Behavior should be traceable, readable, and debuggable.

* **Simplicity over cleverness**
  Straightforward architecture teaches more than hidden automation.

* **Control over convenience**
  Developers should understand the full request lifecycle.

* **Learning through building**
  The best way to understand frameworks is to create one.

Clara is both a framework and a study artifact. A place to experiment, break things, and refine understanding of modern PHP design.

---

## Why Clara Exists

Modern frameworks accelerate development, but they also abstract fundamentals. Clara was built to:

* Deconstruct MVC architecture piece by piece
* Study routing, dependency flow, and application bootstrapping
* Explore PHP 8 features in a controlled environment
* Serve as a lightweight foundation for custom experimentation
* Provide a reference implementation without enterprise complexity

It is intentionally minimal.

---

## Requirements

* PHP 8.3+
* Composer 2+

---

## Installation

Install a local copy with the instructions below.

### 1. Install LAMP Stack

It is assumed you already know how to install a LAMP stack.
Laragon is recommended because it simplifies environment setup. It is portable, isolated, fast, and tailored for PHP development with MySQL.

Download: https://laragon.org/download/

---

### 2. Install Composer

Installation guide: https://getcomposer.org/download/

---

### 3. Setup Server

1. Create a dedicated directory for hosting Clara files
2. Clone or copy Clara into the directory
3. Run:

```bash
composer install
```

---

## Usage

* Configuration files: `src/setup/`
* Core framework files: `src/core/`
* Controllers: `src/app/controllers`
* Models: `src/app/models`

Clara follows a traditional MVC separation while keeping the internal flow explicit and easy to trace.

---

## Project Structure

```
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

---

Clara is not about scale.
It is about understanding.

Not about abstraction layers.
About seeing the layers that already exist.

Build with it. Break it. Learn from it.
