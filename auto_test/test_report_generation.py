import config
from selenium.webdriver.common.by import By
from helpers import login_admin


def test_generate_reports(driver, base_url):
    login_admin(driver, base_url)
    driver.get(f"{base_url}/reports/sections")
    assert "reports" in driver.current_url
    driver.get(f"{base_url}/reports/workload")
    assert "reports" in driver.current_url
    driver.get(f"{base_url}/reports/open-rate")
    assert "reports" in driver.current_url
