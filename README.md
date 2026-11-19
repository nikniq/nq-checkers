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

## Operations

`./cx.sh` is the operations helper. It works locally and on the server. Run `./cx.sh help` to list its commands. The main ones:

| Command | What it does |
|---|---|
| `./cx.sh setup` | Install dependencies, create `.env` and the app key, create the database, run migrations |
| `./cx.sh doctor` / `check` | Health check / pre-deploy check |
| `./cx.sh oauth` | Show the redirect URIs and steps to set up Google and nikniq.com sign-in |
| `./cx.sh admin you@example.com` | Grant admin access |
| `./cx.sh backup` / `restore` | Back up or restore the database (SQLite or MySQL) |

`./deploy.sh` deploys over SSH using git. Configure it in a git-ignored `.deploy.env` file:

```bash
DEPLOY_HOST=user@host            # leave unset when running on the server itself
DEPLOY_PATH=/home/user/checkers  # domain document root -> $DEPLOY_PATH/public_html
DEPLOY_URL=https://checkers.example.com
```

The first time, run `./deploy.sh init`. After that, `./deploy.sh` runs the tests, pushes, deploys, migrates, and runs a health check. It rolls back automatically if any step fails. Other commands: `rollback`, `status`, `logs`, `ssh`, `artisan …`, `cx …`.
