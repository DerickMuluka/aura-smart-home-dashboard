# Aura — Smart Home Dashboard

> A full-stack smart home control system that lets you manage every connected device, build automations, track energy usage, and switch themes across an adaptive, animated interface.

![Status](https://img.shields.io/badge/status-active-success)
![PHP](https://img.shields.io/badge/PHP-8.0%2B-777BB4)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1)
![License](https://img.shields.io/badge/license-MIT-blue)

---

## Table of Contents

- [About](#about)
- [Feature Highlights](#feature-highlights)
- [Tech Stack](#tech-stack)
- [Screenshots](#screenshots)
- [Project Structure](#project-structure)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage Walkthrough](#usage-walkthrough)
- [API Reference](#api-reference)
- [Database Schema](#database-schema)
- [Design System](#design-system)
- [Accessibility](#accessibility)
- [Security Notes](#security-notes)
- [Troubleshooting](#troubleshooting)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)

---

## About

**Aura** is a self-hosted smart home dashboard built with plain PHP, MySQL, and vanilla JavaScript. It gives you a single, cohesive interface to control lights, thermostats, locks, cameras, speakers, plugs, fans, and sensors — and to orchestrate them with trigger-based automation rules.

Unlike tutorial-grade dashboards, Aura ships with:

- A real backend (PDO prepared statements, bcrypt, session auth)
- A schema designed for per-user data isolation
- A WCAG AA-contrast design system with light and dark themes
- An animated marketing landing page and a working application behind auth
- Every API response validated client-side before rendering

No frameworks. No build step. Drop it into any Apache/Nginx + PHP host and run.

---

## Feature Highlights

### Authentication
- Split-screen login and registration with animated gradient blobs
- Show/hide password toggle and live password strength meter
- Server-side validation with inline error display
- Registration redirects to login — no silent auto-login
- Session-guarded pages and JSON-aware 401 responses for API calls

### Device management
- Eight device types: `light`, `thermostat`, `lock`, `camera`, `speaker`, `plug`, `fan`, `sensor`
- Create, toggle, and delete devices
- Search (debounced), filter by type, filter by room
- Room tabs across the top of the grid
- Each device shows live value, connection status, and per-type icon

### Automations
- Rule builder: trigger → action
- Trigger types: time of day, temperature threshold, motion detected, at sunset
- Enable/pause a rule without deleting it
- Rules join to their action device for readable summaries

### Analytics
- Live summary: today, 7-day total, peak day
- Animated 7-day energy bar chart with hover tooltips
- Device-type breakdown donut rendered with CSS conic-gradient
- Recent events table with severity chips (`info` / `warning` / `critical`)
- One-click CSV export of both daily energy and event log

### Dashboard
- Overview metrics: devices online, energy today, avg temperature, active automations
- Quick controls list with inline toggle switches
- Activity timeline with per-severity dots
- Sticky page header with live clock and theme toggle
- Auto-refresh every 30 seconds

### Global experience
- Light and dark themes, persisted to `localStorage`, applied before first paint (no flash)
- Fully responsive: desktop → tablet → mobile, with a bottom tab bar under 820px
- Animated marketing hero: drifting grid, floating blur blobs, floating preview cards
- Toast notifications with a progress bar
- Promise-based confirmation modal
- Keyboard shortcut: `/` focuses search on pages that have one
- Respects `prefers-reduced-motion`

---

## Tech Stack

| Layer | Choice | Notes |
|-------|--------|-------|
| Backend | PHP 8.0+ | PDO prepared statements throughout |
| Database | MySQL 5.7+ / MariaDB 10.3+ | InnoDB, foreign keys with cascades |
| Frontend | Vanilla JS (ES2017+) | Modular per-page files, no bundler |
| Styling | Modern CSS | Custom properties, grid, conic-gradient, backdrop-filter |
| Fonts | Space Grotesk + Inter | Loaded from Google Fonts |
| Icons | Inline SVG | Hand-curated set in `includes/icons.php` |

No npm, no Composer, no build pipeline required.

---

## Screenshots

> Placeholder paths — replace with your own captures.

| Landing | Login | Dashboard |
|---------|-------|-----------|
| `docs/landing.png` | `docs/login.png` | `docs/dashboard.png` |

| Devices | Automations | Analytics |
|---------|-------------|-----------|
| `docs/devices.png` | `docs/automation.png` | `docs/analytics.png` |

---

## Project Structure
aura-smart-home-dashboard/
│
├── .gitignore
├── README.md
├── LICENSE
│
├── config/
│ └── database.php # PDO factory + json_response helper
│
├── database/
│ └── schema.sql # Full schema, ready to import
│
├── includes/
│ ├── auth-check.php # Session guard, JSON-aware for API
│ ├── footer.php # Loads global.js and per-page script
│ ├── header.php # Theme bootstrap + per-page CSS
│ └── icons.php # SVG icon library
│
├── api/
│ ├── analytics.php # 7-day energy, type breakdown, events
│ ├── auth.php # Login + registration
│ ├── automation.php # CRUD for rules
│ ├── devices.php # List, summary, toggle, create, delete
│ ├── events.php # Paginated event feed
│ └── rooms.php # List, create, delete rooms
│
├── assets/
│ ├── css/
│ │ ├── analytics.css
│ │ ├── auth.css
│ │ ├── automation.css
│ │ ├── dashboard.css
│ │ ├── devices.css
│ │ ├── global.css # Tokens, buttons, nav, toast, animations
│ │ └── landing.css
│ └── js/
│ ├── analytics.js
│ ├── auth.js
│ ├── automation.js
│ ├── dashboard.js
│ ├── devices.js
│ ├── global.js # SH namespace, api(), toast, theme
│ ├── landing.js
│ └── theme.js # Pre-paint theme application
│
├── index.php # Landing page
├── login.php
├── register.php
├── dashboard.php
├── devices.php
├── automation.php
├── analytics.php
└── logout.php


---

## Requirements

- PHP **8.0 or newer** with `pdo_mysql`, `json`, and `session` extensions
- MySQL **5.7+** or MariaDB **10.3+**
- A web server: Apache with `mod_rewrite`, Nginx, or PHP's built-in server
- A modern browser (Chrome/Edge/Firefox/Safari — last two major versions)

---

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/<your-username>/aura-smart-home-dashboard.git
cd aura-smart-home-dashboard

2. Create the database
Using the MySQL CLI:

bash
mysql -u root -p < database/schema.sql
Or import database/schema.sql through phpMyAdmin / Adminer / your GUI of choice.

The schema creates a smart_home database with six tables:

users

rooms

devices

automations

energy_log

events

3. Configure credentials
Open config/database.php and adjust:

php
define('DB_HOST', 'localhost');
define('DB_NAME', 'smart_home');
define('DB_USER', 'root');
define('DB_PASS', '');
4. Serve the app
Option A — XAMPP / MAMP / Laragon

Drop the folder into your htdocs (or equivalent) and open:

text
http://localhost/aura-smart-home-dashboard/
Option B — PHP built-in server

bash
php -S localhost:8000
Then open http://localhost:8000.

5. Create your account
Visit /register.php, fill in the form, submit. You will be redirected to the login page — sign in with the same credentials.

Four default rooms (Living Room, Bedroom, Kitchen, Bathroom) are seeded automatically for every new user.

Configuration
Everything you typically need to change lives in config/database.php:

php
define('DB_HOST', 'localhost');  // MySQL host
define('DB_NAME', 'smart_home'); // Database name
define('DB_USER', 'root');       // Database user
define('DB_PASS', '');           // Database password
To change theme colors, edit the CSS custom properties in assets/css/global.css:

css
:root {
  --accent-teal: #0d9488;
  --accent-indigo: #4f46e5;
  /* ... */
}
[data-theme="dark"] {
  --accent-teal: #2dd4bf;
  --accent-indigo: #818cf8;
  /* ... */
}
To change the sticky header height, adjust --header-height in the same file.

Usage Walkthrough
First session
Register an account — you'll land on the login page.

Sign in — you'll land on the dashboard.

The dashboard shows zeros because you haven't added devices yet.

Add your first device
Click Devices in the sidebar.

Click + Add Device.

Enter a name (e.g. Bedroom Lamp), pick a type (Light), pick a room.

Submit — the device appears in the grid.

Control devices
Toggle any device via the switch in the card or in the dashboard's Quick Controls.

Toggling writes to the DB and logs an event.

Delete a device from its card — a confirmation dialog appears first.

Build an automation
Click Automations in the sidebar.

Click + New Rule.

Fill in:

Name — e.g. Evening Lights

Trigger Type — Time of day

Trigger Value — 18:30

Action Device — the device created above

Action Value — Turn on

Submit — the rule appears in the list, enabled by default.

Toggle it off to pause. Toggle back on to re-enable.

Read analytics
Click Analytics in the sidebar.

You get today's usage, a 7-day total, a peak day, an animated bar chart, a donut breakdown, and a recent events table.

Click Export CSV to download a file containing both the daily energy numbers and the event log.

Theme and layout
Toggle the sun/moon icon in any page's header to switch between light and dark.

Your choice persists via localStorage['aura-theme'].

Resize the browser below 820px to see the mobile tab bar.

Resize below 500px to see single-column card layouts.

Keyboard shortcut
Press / on the Devices page to focus the search input.