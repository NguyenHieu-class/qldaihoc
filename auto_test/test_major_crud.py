from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import Select
from helpers import login_user, wait_for_url_contains, wait_for_visibility, click_when_clickable


def create_major(driver, base_url, code="TMJ"):
    driver.get(f"{base_url}/majors/create")
    Select(driver.find_element(By.ID, "faculty_id")).select_by_index(1)
    driver.find_element(By.ID, "name").send_keys("Test Major")
    driver.find_element(By.ID, "code").send_keys(code)
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']")
    wait_for_visibility(driver, By.XPATH, f"//td[text()='{code}']")


def edit_major(driver, code, new_name="Updated Major"):
    row = driver.find_element(By.XPATH, f"//td[text()='{code}']/..")
    row.find_element(By.CSS_SELECTOR, "a.btn-info").click()
    name_field = driver.find_element(By.ID, "name")
    name_field.clear()
    name_field.send_keys(new_name)
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']")
    wait_for_url_contains(driver, "majors")


def delete_major(driver, code):
    row = driver.find_element(By.XPATH, f"//td[text()='{code}']/..")
    click_when_clickable(driver, By.CSS_SELECTOR, "form button[type='submit']")
    wait_for_url_contains(driver, "majors")


def test_major_crud(driver, base_url, unique_suffix):
    login_user("admin", driver, base_url)
    code = f"TMJ{unique_suffix}"

    try:
        create_major(driver, base_url, code)
        assert "majors" in driver.current_url
        assert code in driver.page_source

        edit_major(driver, code, new_name="Major Updated")
        assert "majors" in driver.current_url
        assert "Major Updated" in driver.page_source
    finally:
        delete_major(driver, code)

    assert "majors" in driver.current_url
    assert code not in driver.page_source
