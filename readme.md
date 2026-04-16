# Správa uživatelů - Nette Aplikace

Testovací zadání pro správu uživatelů postavené na Nette frameworku.
Aplikace umožňuje registraci, přihlašování a evidenci uživatelů.

## Požadavky

- PHP 8.2+
- MySQL
- Composer

## Návod na zprovoznění (pro hodnotitele)

1. **Nainstalujte PHP závislosti** pomocí nástroje Composer:
   ```bash
   composer install
   ```

2. **Vytvořte a naplňte databázi**:
   Přímo v kořenové složce `./` projektu se nachází soubor `database.sql`.
   Přes příkazový řádek jej můžete pohodlně importovat následujícím MySQL příkazem
   (případně použijte Adminer / phpMyAdmin a použijte "Import"):
   ```bash
   # Pro instalace bez výchozího root hesla (Laragon)
   mysql -u root < database.sql
   
   # Pokud vyžadujete zadání hesla
   mysql -u root -p < database.sql
   ```
   *Skript automaticky vytvoří databázi `nette_zadani`, strukturu tabulek (`users`, `login_logs`) a rovnou vloží dva výchozí uživatele (viz níže).*

3. **Spusťte lokální PHP server** (nebo využijte existující Nginx/Apache infrastrukturu):
   ```bash
   php -S localhost:8000 -t www
   ```
   *Projekt se následně otevře na adrese **http://localhost:8000**.*

## Výchozí uživatelské účty
Pro otestování aplikace (a ukázku funkčnosti databáze) slouží tyto profily spojené s insertem v `database.sql`:
- Administrátor: jméno `admin`, heslo `Admin123` *(role admin)*
- Běžný uživatel: jméno `user1`, heslo `Admin123` *(role user)*
*(Hesla jsou v databázi uložena pomocí hashovací funkce z PHP `password_hash()` v plain formátu pro zkušební účely, funkčnost bcryptu bude zajištěna při první login implementaci).*

## Databázové připojení
Připojení do MySQL se nastavuje v souboru `config/common.neon` (nebo `local.neon`). Standardně se nyní připojuje na `localhost`, s uživatelem `root` bez hesla a vybírá názvem tabulku `nette_zadani`.

## Technologie

- Nette Application (Modular: Front & Admin)
- Nette Database
- Nette Security (Sessions uloženy v `/temp/sessions`)
- Bootstrap 5 (včetně Icons)
- Contributte Datagrid
