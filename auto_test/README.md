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
4. Install dependencies:
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
