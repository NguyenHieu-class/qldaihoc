import config
from helpers import login_user


def test_admin_login(driver, base_url):
    login_user("admin", driver, base_url)
    assert "/dashboard" in driver.current_url


def test_teacher_login(driver, base_url):
    login_user("teacher", driver, base_url)
    assert "/dashboard" in driver.current_url


def test_student_login(driver, base_url):
    login_user("student", driver, base_url)
    assert "/dashboard" in driver.current_url
