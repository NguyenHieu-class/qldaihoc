import config
from selenium.webdriver.common.by import By


def login_admin(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.ADMIN_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.ADMIN_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def login_teacher(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.TEACHER_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.TEACHER_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()


def login_student(driver, base_url):
    driver.get(f"{base_url}/login")
    driver.find_element(By.ID, "email").send_keys(config.STUDENT_EMAIL)
    driver.find_element(By.ID, "password").send_keys(config.STUDENT_PASSWORD)
    driver.find_element(By.CSS_SELECTOR, "button[type='submit']").click()
