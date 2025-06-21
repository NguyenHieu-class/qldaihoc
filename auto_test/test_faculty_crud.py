import config
from selenium.webdriver.common.by import By
from helpers import login_admin


def create_faculty(driver, base_url, name="Test Faculty"):
    driver.get(f"{base_url}/faculties/create")
    driver.find_element(By.ID, "name").send_keys(name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def edit_faculty(driver, base_url, new_name="Updated Faculty"):
    driver.find_element(By.LINK_TEXT, "Edit").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def delete_faculty(driver):
    driver.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()


def test_faculty_crud(driver, base_url):
    login_admin(driver, base_url)
    create_faculty(driver, base_url)
    assert "faculties" in driver.current_url

    edit_faculty(driver, base_url)
    assert "faculties" in driver.current_url

    delete_faculty(driver)
    assert "faculties" in driver.current_url
