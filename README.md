# Twitch Analytics API

[![CI](https://github.com/Satos04/TwitchAnalytics-showcase/actions/workflows/ci.yml/badge.svg)](https://github.com/Satos04/TwitchAnalytics-showcase/actions/workflows/ci.yml)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php&logoColor=white)
![Lumen](https://img.shields.io/badge/Lumen-11-E74430?logo=laravel&logoColor=white)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

A REST API that turns raw Twitch data into analytics: search game categories and get live statistics for the most popular games (streams, total and average viewers, top streamer). Access is protected with API keys exchanged for short-lived JWTs.

Built with **PHP 8.3 + Lumen**, following a **hexagonal (ports & adapters) architecture** and covered by unit and integration tests.

## Features

- Self-service registration: an email gets you a unique API key.
- Token endpoint exchanging email + API key for a signed JWT (3-day expiry).
- Category search backed by the Twitch Helix API.
- Top games analytics: combines the Twitch "top games" and "streams" endpoints and computes per-game statistics.
- Input validation and consistent JSON error responses.

## Tech stack

| Area | Tools |
| --- | --- |
| Runtime | PHP 8.3, Lumen 11, Eloquent |
| Auth | `firebase/php-jwt` |
| Storage | MySQL 8 (SQLite in-memory for tests) |
| Quality | PHPUnit 10, PHPStan (level 5), PHP-CS-Fixer / PHPCS (PSR-12), PHPMD, GrumPHP |
| Tooling | Docker Compose, GitHub Actions, Postman collection |

## Architecture

Dependencies point inwards: the domain knows nothing about HTTP, Twitch or the database.

```
Domain/            Entities and ports (repository / cipher interfaces)
Application/       One use case per feature (Register, Token, Search, Games)
Infrastructure/    Adapters implementing the ports
  Http/            Controllers, validators, auth middleware
  Twitch/          Helix HTTP client and repositories
  Persistence/     Eloquent users repository, file-based signing key
  Authentication/  JWT cipher, API key generator
```

Use cases depend only on interfaces, which are bound to their adapters in `bootstrap/app.php`. That lets the tests swap Twitch, the database or the JWT library for hand-written doubles (`tests/Doubles`).

## Getting started

### With Docker

```bash
cp .env.docker.example .env.docker   # then fill in TWITCH_CLIENT_ID and TWITCH_TOKEN
docker compose up --build
```

The API is served at `http://localhost:8000`. Check it with `curl http://localhost:8000/health`.

### Locally

Requires PHP 8.2+, Composer and a MySQL database (create the `users` table with `docker/db/init.sql`).

```bash
composer install
cp .env.example .env                 # fill in DB and Twitch credentials
php -S localhost:8000 -t public
```

The JWT signing key (`key.bin`) is generated automatically on first use.

### Configuration

| Variable | Description |
| --- | --- |
| `APP_URL` | Used as the `iss` claim of issued tokens |
| `DB_*` | MySQL connection settings |
| `JWT_SECRET` | Path of the signing key file (default `./key.bin`) |
| `TWITCH_CLIENT_ID` | Client ID of your app in the [Twitch console](https://dev.twitch.tv/console) |
| `TWITCH_TOKEN` | Twitch app access token used to call Helix |

## API reference

| Method | Path | Auth | Description |
| --- | --- | --- | --- |
| GET | `/health` | no | Liveness check |
| POST | `/register` | no | Create a user and get an API key |
| POST | `/token` | no | Exchange email + API key for a JWT |
| GET | `/analytics/search?query=` | Bearer | Search Twitch categories |
| GET | `/analytics/games?limit=` | Bearer | Stats for the top 1-3 games |

### `POST /register`

```bash
curl -X POST http://localhost:8000/register \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com"}'
```

```json
{ "email": "test@example.com", "api_key": "3f9a..." }
```

Status codes: `201` created, `400` invalid email, `409` email already registered.

### `POST /token`

```bash
curl -X POST http://localhost:8000/token \
  -H "Content-Type: application/json" \
  -d '{"email": "test@example.com", "api_key": "3f9a..."}'
```

```json
{ "token": "eyJ0eXAiOiJKV1QiLC...", "expires_in": 259200 }
```

Status codes: `200`, `400` invalid body, `401` wrong credentials.

### `GET /analytics/search`

```bash
curl "http://localhost:8000/analytics/search?query=league" \
  -H "Authorization: Bearer <token>"
```

```json
{
  "data": [
    { "id": "21779", "name": "League of Legends", "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/21779-{width}x{height}.jpg" }
  ],
  "pagination": { "cursor": "eyJiIjpudWxsLCJhIjp7Ik9mZnNldCI6M319" }
}
```

Status codes: `200`, `400` missing `query`, `401` invalid or expired token, `500` internal or Twitch error.

### `GET /analytics/games`

Fetches the current most popular games and up to 100 live streams of each, then aggregates them.

```bash
curl "http://localhost:8000/analytics/games?limit=2" \
  -H "Authorization: Bearer <token>"
```

```json
[
  {
    "id": "32399",
    "name": "Counter-Strike",
    "box_art_url": "https://static-cdn.jtvnw.net/ttv-boxart/32399-285x380.jpg",
    "total_streams": 41,
    "total_viewers": 215954,
    "average_viewers": 5267,
    "top_streamer": { "user_id": "233741947", "user_name": "StRoGo", "viewer_count": 39179 }
  }
]
```

Status codes: `200`, `400` `limit` missing or outside 1-3, `401` invalid or expired token, `500` internal or Twitch error.

## Testing and quality

```bash
composer test                 # PHPUnit (unit + integration)
composer phpstan              # static analysis
composer lint                 # code style check
```

The Postman collection in [`postman/`](postman/TwitchAnalytics.postman_collection.json) exercises every endpoint (happy path, bad request, unauthorized, conflict). Set its `Base URL` variable to `http://localhost:8000`.

GitHub Actions runs code style, PHPStan and PHPUnit on every push and pull request. GrumPHP runs the same checks as a pre-commit hook and enforces the `[#issue] - Message` commit format.

## License

[MIT](LICENSE) © Jon Satóstegui
