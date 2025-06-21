import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_admin


def create_teacher(driver, base_url, teacher_id="GVTEST"):
    driver.get(f"{base_url}/teachers/create")
    driver.find_element(By.ID, "teacher_id").send_keys(teacher_id)
    Select(driver.find_element(By.ID, "faculty_id")).select_by_index(1)
    Select(driver.find_element(By.ID, "degree_id")).select_by_index(1)
    driver.find_element(By.ID, "first_name").send_keys("Test")
    driver.find_element(By.ID, "last_name").send_keys("Teacher")
    Select(driver.find_element(By.ID, "gender")).select_by_value("Nam")
    driver.find_element(By.ID, "date_of_birth").send_keys("1990-01-01")
    driver.find_element(By.ID, "email").send_keys("teacher_test@example.com")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def edit_teacher(driver, teacher_id, new_last="Updated"):
    row = driver.find_element(By.XPATH, f"//td[text()='{teacher_id}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    last_name = driver.find_element(By.ID, "last_name")
    last_name.clear()
    last_name.send_keys(new_last)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def delete_teacher(driver, teacher_id):
    row = driver.find_element(By.XPATH, f"//td[text()='{teacher_id}']/..")
    row.find_element(By.CSS_SELECTOR, "form button[type='submit']").click()
    time.sleep(1)


def test_teacher_crud(driver, base_url):
    login_admin(driver, base_url)
    teacher_id = "GVTEST"
    create_teacher(driver, base_url, teacher_id)
    assert "teachers" in driver.current_url
    assert teacher_id in driver.page_source

    edit_teacher(driver, teacher_id, new_last="Updated")
    assert "teachers" in driver.current_url
    assert "Updated" in driver.page_source

    delete_teacher(driver, teacher_id)
    assert "teachers" in driver.current_url
    assert teacher_id not in driver.page_source
