import config
from selenium.webdriver.common.by import By
from helpers import login_user, click_when_clickable


def test_grade_entry(driver, base_url):
    login_user("teacher", driver, base_url)
    driver.get(f"{base_url}/grades")
    click_when_clickable(driver, By.LINK_TEXT, "Edit")
    score_input = driver.find_element(By.NAME, "score")
    score_input.clear()
    score_input.send_keys("90")
    click_when_clickable(driver, By.CSS_SELECTOR, "button[type='submit']")
    assert "grades" in driver.current_url
