# Správa uživatelů - Nette Aplikace

Testovací zadání pro správu uživatelů postavené na Nette frameworku. Aplikace umožňuje registraci, přihlašování a evidenci uživatelů.

## Požadavky

- PHP 8.2+
- MySQL
- Composer

## Lokální spuštění bez instalace dalšího serveru

```bash
C:\php-8.2.30\php.exe -S localhost:8000 -t www
```
*(Pokud máte `php` přidané v systémové proměnné PATH, stačí spustit jen `php -S localhost:8000 -t www`)*

Projekt pak najdete v prohlížeči na adrese **http://localhost:8000**.

## Databáze

Pro plnou funkčnost je vyžadována MySQL databáze. 
Zatím aplikace počítá s připojením přes výchozí pověření (lze upravit v `config/common.neon`):
- Uživatel: `root`
- Heslo: *(prázdné)*
- Databáze: `nette_users`

*(Pozn.: SQL dotazy pro vytvoření databáze a základních řádků zatím nejsou k dispozici a budou doplněny později).*

## Technologie

- Nette Application (Modular: Front & Admin)
- Nette Database
- Nette Security (Sessions uloženy v `/temp/sessions`)
- Bootstrap 5 (včetně Icons)
- Contributte Datagrid
