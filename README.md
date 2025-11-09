# Trinax Arbetsprov - Tidrapportering

En tidrapporteringsapplikation med React, TypeScript, PHP (Slim Framework) och MySQL som integrerar med Trinax REST API.

## Snabbstart

1. Kopiera `.env.example` till `.env`
2. Lägg till din `TRINAX_API_KEY` i `.env`
3. Kör `docker-compose up -d`
4. Öppna [http://localhost:5173](http://localhost:5173)

## Tjänster

- Frontend: http://localhost:5173
- Backend API: http://localhost:8000/api
- phpMyAdmin: http://localhost:8080 (user: `trinax_user`, pass: `trinax_pass`)

## Funktioner

- Lista och filtrera tidrapporter (arbetsplats, datum)
- Skapa nya rapporter med bilduppladdning
- Bilder serveras via API-endpoint för säker åtkomst

**Tech:** React 19, TypeScript, PHP 8.2, Slim, MySQL, Docker
