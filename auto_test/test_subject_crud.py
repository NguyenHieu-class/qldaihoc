import config
from selenium.webdriver.common.by import By
from helpers import login_user, wait_for_url_contains, wait_for_visibility


def test_subject_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    name = f"Test Subject {unique_suffix}"
    updated = f"Updated Subject {unique_suffix}"

    try:
        driver.get(f"{base_url}/subjects/create")
        driver.find_element(By.ID, "name").send_keys(name)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        wait_for_visibility(driver, By.XPATH, f"//td[text()='{name}']")
        assert "subjects" in driver.current_url
        assert name in driver.page_source
        driver.find_element(By.LINK_TEXT, "Edit").click()
        field = driver.find_element(By.ID, "name")
        field.clear()
        field.send_keys(updated)
        driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
        wait_for_url_contains(driver, "subjects")
        assert "subjects" in driver.current_url
        assert updated in driver.page_source
    finally:
        driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
        wait_for_url_contains(driver, "subjects")

    assert "subjects" in driver.current_url
    assert updated not in driver.page_source
