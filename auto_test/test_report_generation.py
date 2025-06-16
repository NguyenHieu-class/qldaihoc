import config
from selenium.webdriver.common.by import By


def login_admin(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.ADMIN_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.ADMIN_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def test_generate_reports(driver, base_url):
    login_admin(driver, base_url)
    driver.get(f"{base_url}/reports/sections")
    assert "reports" in driver.current_url
    driver.get(f"{base_url}/reports/workload")
    assert "reports" in driver.current_url
    driver.get(f"{base_url}/reports/open-rate")
    assert "reports" in driver.current_url
