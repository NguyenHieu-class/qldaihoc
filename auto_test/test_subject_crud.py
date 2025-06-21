import config
import time
from selenium.webdriver.common.by import By
from helpers import login_user


def test_subject_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    name = f"Test Subject {unique_suffix}"
    updated = f"Updated Subject {unique_suffix}"

    try:
        driver.get(f"{base_url}/subjects/create")
        driver.find_element(By.ID, "name").send_keys(name)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(1)
        assert "subjects" in driver.current_url
        assert name in driver.page_source
        driver.find_element(By.LINK_TEXT, "Edit").click()
        field = driver.find_element(By.ID, "name")
        field.clear()
        field.send_keys(updated)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        time.sleep(1)
        assert "subjects" in driver.current_url
        assert updated in driver.page_source
    finally:
        driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
        time.sleep(1)

    assert "subjects" in driver.current_url
    assert updated not in driver.page_source
