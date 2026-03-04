# Quick Troubleshooting & Testing Guide

> ⚠️ **Offline styling:** The system uses UIKit for layout and notifications. If pages look unstyled or you receive JavaScript errors about `UIkit`, make sure you've placed the CSS/JS files under `assets/uikit/` as described in the README. A placeholder file exists that you can replace with the real library.


## STEP 1: Check Database Connection

Open your browser and go to:
```
http://localhost/scheduling_system/test_db.php
```

You should see one of these responses:
- **SUCCESS**: `{"success":true,"message":"Database connected and active enrollment period found/created"}`
- If you see an error, your database connection is broken

## STEP 2: Open Browser Developer Console

**For Google Chrome / Edge:**
1. Press `F12` key
2. Go to "Console" tab
3. Keep this open while testing each module

This shows you real-time error messages from the API

## STEP 3: Test Module 1 (Create Section)

1. Fill in all fields:
   - Section Name: `BSIT-1A` (or any name)
   - Year Level: `4th Year` (or any year)
   - Subject: `Introduction to Computing` (TYPE THIS - it creates automatically)
   - Category: `Major` (Choose one)

2. Click "SAVE SECTION"

3. Check browser console (F12) for messages:
   - **Success message**: "Section BSIT-1A has been registered!"
   - **Error message**: Check what went wrong

4. **Verify in phpMyAdmin**:
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Go to `enrollment_system` → `sections` table
   - You should see your new section!

## STEP 4: Test Module 2 (Assign Timetable)

1. The section you created should appear in the dropdown
2. Click on a time slot (e.g., "07:30 - 09:00" on Monday)
3. You should see the section appear in that cell
4. Check console (F12) for success message

## STEP 5: Test Module 3 (Assign Room)

1. Your section should appear in the dropdown (with schedule now showing)
2. Enter a room name: `RM216` or `206`
3. Click "VALIDATE & SAVE ROOM"
4. Room should appear in the status table

## STEP 6: Test Module 4 (Assign Teacher)

1. Your section should appear in the dropdown
2. Type teacher name: `Juan Dela Cruz` (exactly as in database)
3. Click "ASSIGN INSTRUCTOR"
4. Teacher name should appear in the table

## STEP 7: Test Module 5 (Run Audit)

1. Click "RUN FINAL SYSTEM AUDIT"
2. Should show: "DATABASE AUDIT COMPLETE" (if no conflicts)
3. If you intentionally want to test conflicts, assign the same room to two sections at the same time

---

## COMMON ERROR MESSAGES & SOLUTIONS

### Error: "Failed to save section"
**Cause**: Database not connected or enrollment period missing  
**Fix**: 
1. Check `test_db.php` first
2. Verify MySQL is running in XAMPP
3. Verify database name is `enrollment_system`

### Error: "Missing required fields"
**Cause**: You didn't fill in all form fields  
**Fix**: Fill in Section Name, Subject, and Category

### Error: "No active sections show in dropdown"
**Cause**: No sections created yet  
**Fix**: 
1. Create a section in Module 1 first
2. Wait a moment, then reload the page
3. Check console (F12) for API errors

### Error: "Could not load sections - [error message]"
**Cause**: API is not responding  
**Fix**: 
1. Check that `api_section.php` and `api_schedule.php` exist
2. Verify MySQL is running
3. Open browser console (F12) and note the exact error

### Modules show loading spinner but nothing appears
**Cause**: API taking too long or failed silently  
**Fix**: 
1. Check console (F12) for error messages
2. Verify XAMPP is running
3. Reload the page
4. Run `test_db.php` to verify database

---

## DATABASE VERIFICATION STEPS

### Check if enrollment period was created:
1. Go to phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `enrollment_system` database
3. Open `enrollment_periods` table  
4. Should have at least one row with `is_active = 1`

### Check sections table:
1. Open `sections` table
2. Should show all sections you created
3. Look at columns:
   - `section_code`: Your section name with timestamp
   - `subject_id`: Links to subject
   - `schedule_days`: Should show day (e.g., "Monday") after Module 2
   - `time_start`: Should show time after Module 2
   - `room_id`: Should show after Module 3
   - `faculty_id`: Should show after Module 4

### Check subjects table:
1. Open `subjects` table
2. Should show subjects you typed in Module 1
3. Auto-created with default values

### Check rooms table:
1. Open `rooms` table
2. Should show rooms you created in Module 3
3. Auto-created with default capacity (40) and type (Classroom)

---

## BROWSER CONSOLE DEBUGGING

When you see an error, open the browser console (F12) and look for:

```javascript
// This means the API responded with an error
API Response: {success: false, message: "...error details..."}

// This means the API didn't respond at all
Fetch Error: TypeError: Failed to fetch
```

Copy the error message and check the corresponding API file.

---

## STILL NOT WORKING?

### Check XAMPP Status:
1. Open XAMPP Control Panel
2. Verify **Apache** and **MySQL** are both **Running** (green)
3. If not running, click **Start** for each

### Check File Locations:
```
C:\xampp\htdocs\scheduling_system\
├── module1.html
├── module2.html
├── module3.html
├── module4.html
├── module5.html
├── api_section.php
├── api_schedule.php
├── index.HTML
└── test_db.php
```

### Check Database Credentials:
Both API files must have these settings:
```php
$servername = "localhost";
$db_user = "root";
$db_password = "";     // Leave blank if your MySQL has no password
$dbname = "enrollment_system";
```

If your MySQL has a password, update it in both files.

---

## EXPECTED WORKFLOW

```
Module 1: Create Section + Subject
    ↓ (Saves to database)
Module 2: Assign to Timetable (Day + Time)
    ↓ (Updates sections table)
Module 3: Assign Room
    ↓ (Updates sections.room_id)
Module 4: Assign Teacher
    ↓ (Updates sections.faculty_id)
Module 5: Audit for Conflicts
    ↓ (Reads full sections table, checks for conflicts)
phpMyAdmin: Verify all data saved
```

---

**If this still doesn't work, check F12 Console for the exact error message and share it!**
