# NexaPHP Framework
> **"Build Fast. Scale Limitlessly."**

NexaPHP is a lightweight, scalable, and flexible PHP framework built for modern web applications. Designed to simplify development while maintaining high performance and security, it provides a clean architecture and powerful modular tools.

---

### Vision & Mission
**Vision:** To empower developers to build fast, scalable, and maintainable web applications using a simple yet powerful PHP framework.

**Mission:** To provide a modern PHP development experience that prioritizes performance, flexibility, and developer productivity while remaining lightweight and easy to use.

---

### Core Values
- **Performance First:** Built for speed with minimal overhead.
- **Flexibility:** No rigid rules -- build your way.
- **Scalability:** Designed to grow from small apps to large systems.
- **Security by Default:** Hardened core with security headers and strict typing.
- **Developer Experience:** Clean syntax, simple structure, and powerful tools.

---

### Key Features
- **Service Container:** Powerful DI container for building modular apps.
- **Express-style Routing:** Middleware chaining and route groups.
- **Hardened Core:** Security headers (CSP, HSTS, XSS) built-in.
- **Nexa CLI (nexa):** Scaffold controllers, models, and run migrations.
- **Slim & Fast:** High performance with minimal memory footprint.

---

### Installation
```bash
composer install
cp .env.example .env
php nexa migrate
```

### Developer Guide
```php
// Define routes in routes/web.php
$router->get('/', function($req, $res) {
    return view('home');
});
```

---
**NexaPHP -- Build faster. Scale smarter.**
