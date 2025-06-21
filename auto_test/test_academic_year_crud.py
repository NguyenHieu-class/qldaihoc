import time
from selenium.webdriver.common.by import By
from helpers import login_admin


def create_year(driver, base_url, name="2025-2026"):
    driver.get(f"{base_url}/academic-years/create")
    driver.find_element(By.ID, "name").send_keys(name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def edit_year(driver, name, new_name="2026-2027"):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    field = driver.find_element(By.ID, "name")
    field.clear()
    field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def delete_year(driver, name):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    row.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    time.sleep(1)


def test_academic_year_crud(driver, base_url, unique_suffix):
    login_admin(driver, base_url)
    name = f"2025-2026-{unique_suffix}"

    try:
        create_year(driver, base_url, name)
        assert "academic-years" in driver.current_url
        assert name in driver.page_source

        edit_year(driver, name, new_name="2026-2027")
        assert "academic-years" in driver.current_url
        assert "2026-2027" in driver.page_source
    finally:
        delete_year(driver, "2026-2027")

    assert "academic-years" in driver.current_url
    assert "2026-2027" not in driver.page_source
