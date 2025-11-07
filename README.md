<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/Laravel/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Packagist Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dm/laravel/framework" alt="Packagist Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# Uptime Monitor

A robust and easy-to-use uptime monitoring application built with Laravel and Filament. This application allows you to keep track of your websites and services, ensuring they are always online and performing as expected.

## Features

-   **Website/Service Monitoring:** Monitor the uptime and response time of any URL.
-   **Configurable Check Intervals:** Set custom intervals for how often each monitor is checked.
-   **Filament Admin Panel:** A beautiful and intuitive admin interface for:
    -   Managing monitors (add, edit, view).
    -   Viewing detailed heartbeat history for each monitor, including HTTP status codes, response times, and error messages.
    -   Disabling create/edit/delete actions for heartbeats from the UI, ensuring data integrity.
-   **Telegram Notifications:** Receive instant notifications directly to your Telegram chat for:
    -   Monitor downtime alerts.
    -   Monitor recovery notifications.
    -   **Initial Check Results:** Get immediate notifications for the first check result (up or down) when a new monitor is added.
-   **Detailed Heartbeat History:** Every check is recorded, providing a comprehensive history of your monitor's performance.

## Installation

1.  Clone the repository:
    ```bash
    git clone [repository_url]
    cd uptime-monitor
    ```
2.  Install Composer dependencies:
    ```bash
    composer install
    ```
3.  Install NPM dependencies and build assets:
    ```bash
    npm install
    npm run dev
    ```
4.  Copy `.env.example` to `.env` and configure your database and Telegram settings:
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    Make sure to set `TELEGRAM_BOT_TOKEN` and `TELEGRAM_CHAT_ID`.
5.  Run database migrations and seed (optional):
    ```bash
    php artisan migrate --seed
    ```
6.  Start the development server:
    ```bash
    php artisan serve
    ```
7.  Run the queue worker (for notifications):
    ```bash
    php artisan queue:work
    ```
    (Alternatively, set `QUEUE_CONNECTION=sync` in your `.env` for synchronous processing in development).
8.  Schedule the heartbeat checks:
    Add the following to your server's cron jobs:
    ```bash
    * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
    ```

## Usage

Access the Filament admin panel by navigating to `/admin` in your browser. From there, you can manage your monitors and view their heartbeat status.

## TODO (or Milestone)

- To make the Heartbeats data appear inside each monitors single view page instead maybe instead of making another page or menu for it.
- Modify default dashboard to show Monitor and Heartbeat stats.
- Add more notification channels (e.g., email, Slack).
- Implement user authentication and roles for multi-user support.
- Monitor not only HTTP(S) but also other protocols (e.g., ping, TCP).
- Make the protocols selectable and create prefixes for the heartbeat names (e.g., `http://` or `smb://`).


## Contributing

Feel free to contribute to the development of this uptime monitor.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
