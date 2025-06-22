import config
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.common.exceptions import TimeoutException, ElementClickInterceptedException
import time


def delay():
    """Pause execution for ``config.ACTION_DELAY`` seconds if configured."""
    if config.ACTION_DELAY > 0:
        time.sleep(config.ACTION_DELAY)


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
    delay()
    wait_for_visibility(driver, By.ID, "email", timeout).send_keys(credentials_email)
    delay()
    wait_for_visibility(driver, By.ID, "password", timeout).send_keys(credentials_password)
    delay()

    previous_url = driver.current_url
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']", timeout)
    delay()

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


def wait_for_visibility(driver, by, locator, timeout=20):
    """Return element once visible."""
    element = WebDriverWait(driver, timeout).until(
        EC.visibility_of_element_located((by, locator))
    )
    delay()
    return element


def wait_for_clickable(driver, by, locator, timeout=20):
    """Return element once clickable."""
    element = WebDriverWait(driver, timeout).until(
        EC.element_to_be_clickable((by, locator))
    )
    delay()
    return element


def click_when_clickable(driver, by, locator, timeout=20):
    """Click element once it becomes clickable with retry on interception."""
    for attempt in range(3):
        try:
            wait_for_clickable(driver, by, locator, timeout).click()
            delay()
            return
        except ElementClickInterceptedException:
            time.sleep(1)
    raise ElementClickInterceptedException(
        f"Element {locator} could not be clicked after retries"
    )


def wait_until_element_disappear(driver, by, locator, timeout=20):
    """Wait until the specified element is no longer present."""
    WebDriverWait(driver, timeout).until_not(
        EC.presence_of_element_located((by, locator))
    )
    delay()


def wait_for_url_contains(driver, text, timeout=10):
    """Wait until current URL contains given text."""
    result = WebDriverWait(driver, timeout).until(EC.url_contains(text))
    delay()
    return result
