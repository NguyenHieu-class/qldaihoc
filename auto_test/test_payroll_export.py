from selenium.webdriver.common.by import By
from helpers import login_user


def test_payroll_export(driver, base_url):
    login_user("admin", driver, base_url)
    driver.get(f"{base_url}/payrolls")
    driver.find_element(By.LINK_TEXT, "Xuất PDF").click()
    assert "payrolls/export" in driver.current_url
