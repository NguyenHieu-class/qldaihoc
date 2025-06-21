import config
from selenium.webdriver.common.by import By
from helpers import login_user, click_when_clickable


def test_student_enrollment(driver, base_url):
    login_user("student", driver, base_url)
    driver.get(f"{base_url}/enrollments")
    click_when_clickable(driver, By.CSS_SELECTOR, "a.enroll-btn")
    assert "enrollments" in driver.current_url
    click_when_clickable(driver, By.CSS_SELECTOR, "form.drop-form button[type='submit']")
    assert "enrollments" in driver.current_url
