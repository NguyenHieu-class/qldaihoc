import config
from selenium.webdriver.common.by import By


def login_student(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.STUDENT_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.STUDENT_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def test_student_enrollment(driver, base_url):
    login_student(driver, base_url)
    driver.get(f"{base_url}/enrollments")
    driver.find_element(By.CSS_SELECTOR, "a.enroll-btn").click()
    assert "enrollments" in driver.current_url
    driver.find_element(By.CSS_SELECTOR, "form.drop-form button[type='submit']").click()
    assert "enrollments" in driver.current_url
