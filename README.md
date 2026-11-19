# nq-checkers

Checkers game built on Laravel 13. The web root is **`public_html/`**, not `public/`. It is set in `bootstrap/app.php` through `usePublicPath()`.

- Anyone can play without an account, either against the computer (easy, medium or hard) or two players on one device.
- Registered users have their games against the computer saved. They get a score history at `/scores` and appear on the leaderboard at `/leaderboard`.

## Setup

```bash
composer install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite && php artisan migrate
php artisan serve
```

To deploy on shared hosting, point the domain's document root at `public_html/`.

## Scoring
Points are calculated on the server: win = 100 + 10 × pieces left, draw = 25, loss = 0. Add 2 for each capture, then multiply by the difficulty (×1, ×2 or ×3).
The game runs in the browser, so a determined user could send fake results. To prevent that, the server would have to validate every move.
