# Deníček

Jednoduchá CRUD Laravel aplikace, která umožňuje uložení důležitých vzpomínek.

- Podpora pro více uživatelů
- Šifrovaná SQLite DB
- Možnost přiložení souboru k danému dni (šifrované na disku) 

```shell
git clone https://github.com/lopatar/Denicek.git && cd Denicek
composer install
cp .env.example .env
php artisan migrate
php artisan serve
```