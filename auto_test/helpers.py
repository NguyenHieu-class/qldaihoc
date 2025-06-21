import config
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException


def login_user(role, driver, base_url, timeout=10):
    """Log in as the specified role and wait for dashboard redirect.

    Parameters
    ----------
    role: str
        One of "admin", "teacher", or "student".
    driver: WebDriver
        Selenium WebDriver instance.
    base_url: str
        Base URL for the application.
    timeout: int
        Seconds to wait for the login redirect.
    """
    credentials_email = getattr(config, f"{role.upper()}_EMAIL")
    credentials_password = getattr(config, f"{role.upper()}_PASSWORD")

    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(credentials_email)
    driver.find_element(By.ID, "password").send_keys(credentials_password)

    previous_url = driver.current_url
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()

    try:
        WebDriverWait(driver, timeout).until(EC.url_changes(previous_url))
        WebDriverWait(driver, timeout).until(EC.url_contains("/dashboard"))
    except TimeoutException:
        error_message = None
        error_elems = driver.find_elements(By.CLASS_NAME, "alert")
        if error_elems:
            error_message = error_elems[0].text
        raise AssertionError(f"Login failed for {role}: {error_message}")


def login_admin(driver, base_url):
    login_user("admin", driver, base_url)


def login_teacher(driver, base_url):
    login_user("teacher", driver, base_url)


def login_student(driver, base_url):
    login_user("student", driver, base_url)
