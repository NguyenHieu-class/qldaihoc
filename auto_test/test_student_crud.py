import config
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
import time
from helpers import login_admin


def create_student(driver, base_url, name="Test Student", email="student_test@example.com"):
    driver.get(f"{base_url}/students/create")
    driver.find_element(By.ID, "name").send_keys(name)
    driver.find_element(By.ID, "email").send_keys(email)
    driver.find_element(By.ID, "password").send_keys("password")
    Select(driver.find_element(By.ID, "class_id")).select_by_index(1)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def edit_student(driver, base_url, new_name="Updated Student"):
    driver.find_element(By.LINK_TEXT, "Edit").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def delete_student(driver):
    driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()


def test_student_crud(driver, base_url):
    login_admin(driver, base_url)
    create_student(driver, base_url)
    assert "students" in driver.current_url

    edit_student(driver, base_url)
    assert "students" in driver.current_url

    delete_student(driver)
    assert "students" in driver.current_url
