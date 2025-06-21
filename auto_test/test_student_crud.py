import config
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_user, wait_for_url_contains, wait_for_visibility


def create_student(driver, base_url, name="Test Student", email="student_test@example.com"):
    driver.get(f"{base_url}/students/create")
    driver.find_element(By.ID, "name").send_keys(name)
    driver.find_element(By.ID, "email").send_keys(email)
    driver.find_element(By.ID, "password").send_keys("password")
    Select(driver.find_element(By.ID, "class_id")).select_by_index(1)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait_for_visibility(driver, By.XPATH, f"//td[text()='{name}']")


def edit_student(driver, base_url, new_name="Updated Student"):
    driver.find_element(By.LINK_TEXT, "Edit").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    wait_for_url_contains(driver, "students")


def delete_student(driver):
    driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    wait_for_url_contains(driver, "students")


def test_student_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    student_name = f"Test Student {unique_suffix}"
    updated_name = f"Updated Student {unique_suffix}"
    email = f"student_test_{unique_suffix}@example.com"

    try:
        create_student(driver, base_url, name=student_name, email=email)
        assert "students" in driver.current_url
        assert student_name in driver.page_source

        edit_student(driver, base_url, new_name=updated_name)
        assert "students" in driver.current_url
        assert updated_name in driver.page_source
    finally:
        delete_student(driver)

    assert "students" in driver.current_url
    assert updated_name not in driver.page_source
