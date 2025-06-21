import config
from selenium.webdriver.common.by import By
from helpers import login_user, wait_for_url_contains, wait_for_visibility


def create_faculty(driver, base_url, name="Test Faculty"):
    driver.get(f"{base_url}/faculties/create")
    driver.find_element(By.ID, "name").send_keys(name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait_for_visibility(driver, By.XPATH, f"//td[text()='{name}']")


def edit_faculty(driver, base_url, new_name="Updated Faculty"):
    driver.find_element(By.LINK_TEXT, "Edit").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait_for_url_contains(driver, "faculties")


def delete_faculty(driver):
    driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    wait_for_url_contains(driver, "faculties")


def test_faculty_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    faculty_name = f"Test Faculty {unique_suffix}"
    updated_name = f"Updated Faculty {unique_suffix}"

    try:
        create_faculty(driver, base_url, name=faculty_name)
        assert "faculties" in driver.current_url
        assert faculty_name in driver.page_source

        edit_faculty(driver, base_url, new_name=updated_name)
        assert "faculties" in driver.current_url
        assert updated_name in driver.page_source
    finally:
        delete_faculty(driver)

    assert "faculties" in driver.current_url
    assert updated_name not in driver.page_source
