import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_admin


def create_semester(driver, base_url, name="HKTEST"):
    driver.get(f"{base_url}/semesters/create")
    driver.find_element(By.ID, "name").send_keys(name)
    Select(driver.find_element(By.ID, "academic_year_id")).select_by_index(1)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def edit_semester(driver, name, new_name="HKUP"):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def delete_semester(driver, name):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    row.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    time.sleep(1)


def test_semester_crud(driver, base_url):
    login_admin(driver, base_url)
    name = "HKTEST"
    create_semester(driver, base_url, name)
    assert "semesters" in driver.current_url
    assert name in driver.page_source

    edit_semester(driver, name, new_name="HKUP")
    assert "semesters" in driver.current_url
    assert "HKUP" in driver.page_source

    delete_semester(driver, "HKUP")
    assert "semesters" in driver.current_url
    assert "HKUP" not in driver.page_source
