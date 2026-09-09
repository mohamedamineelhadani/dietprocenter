# DietProCenter

Nutrition / diet-consultation website: a public French-language site (home, about,
consultation booking, contact) plus a PHP + MySQL backend with a small admin dashboard.

## Project structure

```
dietprocenter/
├── index.html              Home page (hero + IMC calculator)
├── a-propos.html            About page
├── consultation.html        Consultation booking form
├── contact.html              Contact form
├── css/
│   ├── style.css             Main site styles (design tokens, layout, animations)
│   └── responsive.css        Breakpoints
├── js/
│   └── script.js              Mobile menu, dark-mode toggle, IMC calculator,
│                              scroll-reveal, form submission (fetch -> PHP)
├── images/
│   └── photo.png              Hero image
├── php/
│   ├── db.php                  PDO database connection
│   ├── database.sql            Schema: contacts, consultations, admins (+ default admin)
│   ├── contact_handler.php      Saves contact.html submissions
│   └── consultation_handler.php Saves consultation.html submissions
└── admin/
    ├── login.php                Admin login (session + bcrypt)
    ├── logout.php
    ├── index.php                 Overview / stats
    ├── consultations.php         All consultation requests: search, status update,
    │                            Excel export (filtered by date or all)
    ├── contacts.php               All contact messages: search, "Répondre" (mailto) button
    ├── update_status.php          Handles the status-change form on consultations.php
    ├── export_consultations.php   Streams consultations as an Excel-openable .xls file
    ├── script.js                   Live search filter shared by consultations.php/contacts.php
    └── style.css                  Styles
```

## 1. Set up the database

This creates the `dietprocenter` database with `contacts`, `consultations`, and `admins`
tables, and inserts a default admin account.

Edit `php/db.php` if your MySQL host, database name, user, or password differ from the
defaults (`localhost` / `dietprocenter` / `root` / empty password).

## 2. Run the site

## 3. Admin 

Go to `http://localhost/admin/login.php`.

**Default login** — change the password after first use:
- username: `admin`
- password: `admin123`

What's in the dashboard:
- **index.php** — quick stats (totals for consultations, contacts, pending requests)
- **consultations.php** — full list of consultation requests
  - live search box (filters by name, email, phone, or consultation type as you type)
  - status dropdown per row (Nouveau / Confirmé / Annulé), saved via `update_status.php`
  - **Excel (filtré)** — exports rows whose requested appointment date falls within the
    "Du / Au" range you enter
  - **Tout exporter** — exports every consultation, ignoring the date fields
  - exported file downloads as `.xls` and opens directly in Excel
- **contacts.php** — full list of contact messages, with the same live search, and a
  **Répondre** button per row that opens your email client with the client's address,
  a "Re: <subject>" line, and the original message quoted, ready to send

## Security notes for going to production

- Change the default admin password (`admins.password_hash`, generate a new hash with
  PHP's `password_hash()`).
- Set real, non-empty MySQL credentials in `php/db.php` — don't use `root` with no
  password outside local development.
- Serve the site over HTTPS so session cookies and form submissions aren't sent in
  the clear.