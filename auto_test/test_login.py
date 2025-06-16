import config


def login(driver, base_url, email, password):
    driver.get(f"{base_url}/login")
    driver.find_element("id", "email").send_keys(email)
    driver.find_element("id", "password").send_keys(password)
    driver.find_element("css selector", "button[type='submit']").click()


def test_admin_login(driver, base_url):
    login(driver, base_url, config.ADMIN_EMAIL, config.ADMIN_PASSWORD)
    assert "/dashboard" in driver.current_url


def test_teacher_login(driver, base_url):
    login(driver, base_url, config.TEACHER_EMAIL, config.TEACHER_PASSWORD)
    assert "/dashboard" in driver.current_url


def test_student_login(driver, base_url):
    login(driver, base_url, config.STUDENT_EMAIL, config.STUDENT_PASSWORD)
    assert "/dashboard" in driver.current_url
