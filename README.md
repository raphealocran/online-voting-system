# Online Voting System

A group project (Rapheal, Darliza, Najiyah) — a web-based voting platform with live results visualization. Built as a class project with a PHP + MySQL backend.

## Features

- Intro and homepage with election info
- Voting interface with live results chart (Chart.js)
- User signup and login (PHP + MySQL)
- Policies page

## Tech stack

- HTML5, CSS3, vanilla JavaScript
- Chart.js (CDN) for live results
- PHP (MySQLi) backend
- MySQL database (`users.sql` schema)

## Run locally (full functionality)

Requires PHP + MySQL — easiest with [XAMPP](https://www.apachefriends.org/):

1. Start Apache and MySQL in XAMPP.
2. Create a database named `voting` and import `users.sql` (phpMyAdmin → Import).
3. Copy this folder to `htdocs/online-voting-system`.
4. Check `db_connection.php` — it uses the default XAMPP credentials (`root` / empty password).
5. Visit `http://localhost/online-voting-system/Intro.html`.

## Static preview

A static deployment serves the HTML pages, but voting, login, and signup require the PHP/MySQL backend above.
