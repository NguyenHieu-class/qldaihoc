import os
import shutil

import pytest
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from selenium.webdriver.chrome.service import Service
from webdriver_manager.chrome import ChromeDriverManager

import config


@pytest.fixture(scope="session")
def driver():
    options = Options()
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-dev-shm-usage")

    # Attempt to use a pre-installed Chrome driver to avoid network downloads
    driver_path = os.getenv("CHROME_DRIVER_PATH") or shutil.which("chromedriver")
    if driver_path and os.path.exists(driver_path):
        service = Service(driver_path)
    else:
        # Fallback to webdriver-manager which may download the driver
        service = Service(ChromeDriverManager().install())

    driver = webdriver.Chrome(service=service, options=options)
    driver.implicitly_wait(5)
    yield driver
    driver.quit()


@pytest.fixture
def base_url():
    return config.BASE_URL
