# Automated Browser Tests

This directory contains automated end-to-end tests written with Selenium and Pytest.

## Setup

1. Install Python 3.11+.
2. Install Google Chrome or Chromium. The Selenium tests require a local Chrome/Chromium browser. The `chromedriver` binary is installed automatically via `webdriver-manager`, but you must have Chrome or Chromium installed. Optionally set `CHROME_DRIVER_PATH` if you need to specify a custom driver path.
3. Create and activate a virtual environment:
   ```bash
   python3 -m venv venv
   source venv/bin/activate
   ```
4. Install dependencies with the pinned versions:
   ```bash
   pip install -r requirements.txt
   ```

## Running the Laravel App

Start the Laravel development server in another terminal:

```bash
php artisan serve
```

The application will be available at `http://127.0.0.1:8000` by default.

## Running Tests

Execute tests with Pytest (optionally generating an HTML report):

```bash
pytest --html=report.html
```

Ensure the Laravel server is running before executing the tests.

## Environment Variables

The tests read several settings from environment variables. Defaults are used
when variables are unset:

| Variable | Default | Description |
|----------|---------|-------------|
| `BASE_URL` | `http://127.0.0.1:8000` | Base URL where the Laravel application is served |
| `ADMIN_EMAIL` | `admin@example.com` | Seeded admin account email |
| `ADMIN_PASSWORD` | `password` | Password for the admin account |
| `TEACHER_EMAIL` | `teacher1@example.com` | Seeded teacher account email |
| `TEACHER_PASSWORD` | `password` | Password for the teacher account |
| `STUDENT_EMAIL` | `student1@example.com` | Seeded student account email |
| `STUDENT_PASSWORD` | `password` | Password for the student account |

`CHROME_DRIVER_PATH` may also be set to specify a custom Chrome driver path.
