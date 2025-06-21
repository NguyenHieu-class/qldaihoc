import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_admin


def create_class(driver, base_url, code="CLTEST"):
    driver.get(f"{base_url}/classes/create")
    Select(driver.find_element(By.ID, "major_id")).select_by_index(1)
    driver.find_element(By.ID, "name").send_keys("Test Class")
    driver.find_element(By.ID, "code").send_keys(code)
    year_field = driver.find_element(By.ID, "year")
    year_field.clear()
    year_field.send_keys("2024")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def edit_class(driver, code, new_name="Updated Class"):
    row = driver.find_element(By.XPATH, f"//td[text()='{code}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def delete_class(driver, code):
    row = driver.find_element(By.XPATH, f"//td[text()='{code}']/..")
    row.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    time.sleep(1)


def test_class_crud(driver, base_url, unique_suffix):
    login_admin(driver, base_url)
    code = f"CLTEST{unique_suffix}"

    try:
        create_class(driver, base_url, code)
        assert "classes" in driver.current_url
        assert code in driver.page_source

        edit_class(driver, code, new_name="Class Updated")
        assert "classes" in driver.current_url
        assert "Class Updated" in driver.page_source
    finally:
        delete_class(driver, code)

    assert "classes" in driver.current_url
    assert code not in driver.page_source
