from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_user, wait_for_url_contains, wait_for_visibility, click_when_clickable


def create_semester(driver, base_url, name="HKTEST"):
    driver.get(f"{base_url}/semesters/create")
    driver.find_element(By.ID, "name").send_keys(name)
    Select(driver.find_element(By.ID, "academic_year_id")).select_by_index(1)
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']")
    wait_for_visibility(driver, By.XPATH, f"//td[text()='{name}']")


def edit_semester(driver, name, new_name="HKUP"):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']")
    wait_for_url_contains(driver, "semesters")


def delete_semester(driver, name):
    row = driver.find_element(By.XPATH, f"//td[text()='{name}']/..")
    click_when_clickable(driver, By.CSS_SELECTOR, "form button[type='submit']")
    wait_for_url_contains(driver, "semesters")


def test_semester_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    name = f"HKTEST{unique_suffix}"

    try:
        create_semester(driver, base_url, name)
        assert "semesters" in driver.current_url
        assert name in driver.page_source

        edit_semester(driver, name, new_name="HKUP")
        assert "semesters" in driver.current_url
        assert "HKUP" in driver.page_source
    finally:
        delete_semester(driver, "HKUP")

    assert "semesters" in driver.current_url
    assert "HKUP" not in driver.page_source
