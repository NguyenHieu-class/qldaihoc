import config
from selenium.webdriver.common.by import By
from helpers import login_user


def test_student_enrollment(driver, base_url):
    login_user("student", driver, base_url)
    driver.get(f"{base_url}/enrollments")
    driver.find_element(By.CSS_SELECTOR, "a.enroll-btn").click()
    assert "enrollments" in driver.current_url
    driver.find_element(By.CSS_SELECTOR, "form.drop-form button[type='submit']").click()
    assert "enrollments" in driver.current_url
