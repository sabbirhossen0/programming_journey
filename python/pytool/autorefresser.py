"""
auto_refresher_anywhere.py
Auto refresh (F5) and press Enter every 300–500 seconds.
Works on any active window (browser, app, etc.)

Usage:
  1. pip install pyautogui
  2. Run this script
  3. Keep your desired window active (it will press keys there)
"""

import pyautogui
import time
import random

# Random interval range (in seconds)
MIN_INTERVAL = 300  # 5 minutes
MAX_INTERVAL = 500  # ~8.3 minutes

print("🔁 Auto Refresher started!")
print("Press Ctrl + C in this window to stop.\n")

try:
    while True:
        wait_time = random.randint(MIN_INTERVAL, MAX_INTERVAL)
        print(f"⏳ Waiting {wait_time} seconds before refresh...")
        time.sleep(wait_time)

        # Perform refresh (F5) and Enter
        pyautogui.press('f5')
        time.sleep(1)  # short delay after refresh
        pyautogui.press('enter')

        print("✅ Refreshed and pressed ENTER.")

except KeyboardInterrupt:
    print("\n🛑 Auto Refresher stopped by user.")
