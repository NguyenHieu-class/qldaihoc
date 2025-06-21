import config
from selenium.webdriver.common.by import By
from helpers import login_admin


def test_subject_crud(driver, base_url):
    login_admin(driver, base_url)
    driver.get(f"{base_url}/subjects/create")
    driver.find_element(By.ID, "name").send_keys("Test Subject")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    assert "subjects" in driver.current_url
    driver.find_element(By.LINK_TEXT, "Edit").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys("Updated Subject")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    assert "subjects" in driver.current_url
    driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    assert "subjects" in driver.current_url
