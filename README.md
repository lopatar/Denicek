# Deníček

Laravel aplikace, která umožňuje uložení důležitých vzpomínek.

- Podpora pro více uživatelů
- Šifrovaná SQLite DB
- Možnost přiložení souboru k danému dni (šifrované na disku) 
- Stránkování jednotlivých týdnů
    - Průměrné hodnocení
- **Podpora PWA (Progressive Web App)**

```shell
git clone https://github.com/lopatar/Denicek.git && cd Denicek
composer install
cp .env.example .env
php artisan migrate
php artisan db:seed
php artisan serve
```