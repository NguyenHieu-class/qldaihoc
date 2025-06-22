# Configuration for Selenium tests
import os

# Base URL where the Laravel application is served
BASE_URL = os.getenv("BASE_URL", "http://127.0.0.1:8000")

# Seeded user credentials
ADMIN_EMAIL = os.getenv("ADMIN_EMAIL", "admin@example.com")
ADMIN_PASSWORD = os.getenv("ADMIN_PASSWORD", "password")

TEACHER_EMAIL = os.getenv("TEACHER_EMAIL", "teacher1@example.com")
TEACHER_PASSWORD = os.getenv("TEACHER_PASSWORD", "password")

STUDENT_EMAIL = os.getenv("STUDENT_EMAIL", "student1@example.com")
STUDENT_PASSWORD = os.getenv("STUDENT_PASSWORD", "password")

# Optional delay (in seconds) inserted after each Selenium action. This can be
# useful for visually observing test execution or for debugging timing issues.
ACTION_DELAY = float(os.getenv("ACTION_DELAY", 0))
