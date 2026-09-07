# Rosca (Chama) module

This module implements a Chama-style rotating savings and credit association suitable for Kenyan usage patterns, scaffolded to match Akaunting's module structure.

Installation

1. Ensure modules/ is autoloaded via PSR-4 or your app's module loader. Example in project composer.json:

```json
"autoload": {
  "psr-4": {
    "Modules\\\\": "modules/"
  }
}
```

2. Run composer dump-autoload

3. Publish and run migrations:

```bash
php artisan vendor:publish --tag=rosca-migrations
php artisan migrate
```

4. Optional: publish views or config

```bash
php artisan vendor:publish --tag=rosca-views
php artisan vendor:publish --tag=rosca-config
```

Endpoints

- API: /api/roscas, /api/rosca-members, /api/rosca-contributions
- Web: /roscas
