import config
from selenium.webdriver.common.by import By


def login_teacher(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.TEACHER_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.TEACHER_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def test_grade_entry(driver, base_url):
    login_teacher(driver, base_url)
    driver.get(f"{base_url}/grades")
    driver.find_element(By.LINK_TEXT, "Edit").click()
    score_input = driver.find_element(By.NAME, "score")
    score_input.clear()
    score_input.send_keys("90")
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
    assert "grades" in driver.current_url
