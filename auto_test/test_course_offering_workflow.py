import time
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_user


def create_course_offering(driver, base_url):
    driver.get(f"{base_url}/course-offerings/create")
    checkbox = driver.find_elements(By.CSS_SELECTOR, "input[name='subject_ids[]']")[0]
    checkbox.click()
    Select(driver.find_element(By.ID, "semester_id")).select_by_index(1)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def generate_class_section(driver, base_url):
    driver.get(f"{base_url}/class-sections/create?auto=1")
    Select(driver.find_element(By.NAME, "course_offering_id")).select_by_index(1)
    Select(driver.find_element(By.NAME, "teacher_id")).select_by_index(1)
    Select(driver.find_element(By.NAME, "teaching_rate_id")).select_by_index(1)
    driver.find_element(By.NAME, "number_of_sections").clear()
    driver.find_element(By.NAME, "number_of_sections").send_keys("1")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    time.sleep(1)


def delete_first_course_offering(driver, base_url):
    driver.get(f"{base_url}/course-offerings")
    buttons = driver.find_elements(By.CSS_SELECTOR, "form button[type='submit']")
    if buttons:
        buttons[0].click()
        time.sleep(1)


def delete_first_class_section(driver, base_url):
    driver.get(f"{base_url}/class-sections")
    buttons = driver.find_elements(By.CSS_SELECTOR, "form button[type='submit']")
    if buttons:
        buttons[0].click()
        time.sleep(1)


def test_course_offering_and_class_section(driver, base_url):
    login_user("admin", driver, base_url)

    try:
        create_course_offering(driver, base_url)
        assert "course-offerings" in driver.current_url

        generate_class_section(driver, base_url)
        assert "class-sections" in driver.current_url
    finally:
        delete_first_class_section(driver, base_url)
        delete_first_course_offering(driver, base_url)
