# Automated Browser Tests

This directory contains automated end-to-end tests written with Selenium and Pytest.

## Setup

1. Install Python 3.11+.
2. Create and activate a virtual environment:
   ```bash
   python3 -m venv venv
   source venv/bin/activate
   ```
3. Install dependencies:
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
