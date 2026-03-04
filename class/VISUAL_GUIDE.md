# 🎬 STEP-BY-STEP VISUAL GUIDE

## **OPTION 1: Automatic (Easiest - 10 seconds)**

```
1. Open File Explorer
2. Go to: C:\xampp\htdocs\scheduling_system\
3. Find file: AUTOSTART.bat
4. Double-click it
5. Watch browser open automatically ✨
6. Done! ✅
```

**That's literally it!** The system will start and test itself automatically.

---

## **OPTION 2: Manual (If AUTOSTART doesn't work)**

### Step 1: Open XAMPP Control Panel
- Click Start menu → Search for "XAMPP"
- Click "XAMPP Control Panel"
- You'll see this:

```
┌────────────────────────────┐
│  XAMPP Control Panel v3.x  │
├────────────────────────────┤
│ Apache      [Start] [Stop] │  ← Look for this
│ MySQL       [Start] [Stop] │  ← And this
│ FileZilla   [Start] [Stop] │
│ Mercury     [Start] [Stop] │
└────────────────────────────┘
```

### Step 2: Start Apache
- Click **[Start]** button next to "Apache"
- Wait 3-5 seconds
- It should turn GREEN with checkmark ✅
- If it's red ❌, check TROUBLESHOOTING.md

```
Apache      [Stop] ✅ GREEN = RUNNING
```

### Step 3: Start MySQL
- Click **[Start]** button next to "MySQL"
- Wait 5-10 seconds (MySQL is slower)
- It should turn GREEN with checkmark ✅

```
MySQL       [Stop] ✅ GREEN = RUNNING
```

### Step 4: Open Browser
- Press Windows key + R
- Type: `http://localhost/scheduling_system/START.html`
- Press Enter
- You'll see home page

### Step 5: Check Status
- Page shows: **Database Status**
- Should be GREEN ✅ "Database Connected"
- If RED ❌ "Database NOT connected": 
  - Wait 10 more seconds
  - Refresh page (F5)

---

## **Testing Each Module**

Once you see ✅ "Database Connected", you can test:

### Module 1: Create Sections
**URL:** http://localhost/scheduling_system/module1.html

```
1. Section Name: [TEST                  ]
2. Subject:      [Testing               ]
3. Category:     [General               ]

[SAVE SECTION]

Result: ✅ "Section saved!"
```

### Module 2: Assign Timetable
**URL:** http://localhost/scheduling_system/module2.html

```
1. Select Section: [TEST ▼]
2. Select Day:     [Monday ▼]
3. Start Time:     [08:00 ▼]
4. End Time:       [09:00 ▼]

[SAVE SCHEDULE]

Result: ✅ "Schedule saved!"
```

### Module 3: Assign Rooms
**URL:** http://localhost/scheduling_system/module3.html

```
1. Select Section: [TEST ▼]
2. Room Number:    [101    ]

[ASSIGN ROOM]

Result: ✅ "Room assigned!"
```

### Module 4: Assign Teachers
**URL:** http://localhost/scheduling_system/module4.html

```
1. Select Section:  [TEST          ▼]
2. Teacher Name:    [Mr. Smith     ]

[ASSIGN TEACHER]

Result: ✅ "Teacher assigned!"
```

### Module 5: Check Conflicts
**URL:** http://localhost/scheduling_system/module5.html

```
[SCAN FOR CONFLICTS]

Result: Shows list of conflicts found
        (or "No conflicts" if all clear)
```

---

## **Verify Everything in phpMyAdmin**

**URL:** http://localhost/phpmyadmin

```
1. Look for "enrollment_system" database (left panel)
2. Click it to expand
3. You should see tables:
   □ sections      ← Your created sections
   □ subjects      ← Your created subjects  
   □ rooms         ← Your assigned rooms
   □ employees     ← Teachers database
   □ schedules     ← Your timetables
   □ section_faculty ← Teacher assignments

Click any table to see the data inside!
```

---

## **If It Doesn't Work**

### Apache Won't Start (Red ❌)
**Possible Causes:**
1. Port 80 is busy (another app using it)
2. XAMPP not installed properly
3. Antivirus blocking

**Solutions:**
1. Close any other web servers
2. Try reinstalling XAMPP
3. Check Windows Firewall

### MySQL Won't Start (Red ❌)
**Possible Causes:**
1. MySQL data corrupted
2. Wrong Windows permissions
3. MySQL port (3306) is busy

**Solutions:**
1. Uninstall XAMPP
2. Delete C:\xampp\mysql\data\
3. Reinstall XAMPP fresh

### Database Says "NOT Connected" (Red)
1. **Both Apache AND MySQL must be GREEN first!**
2. Wait 10 seconds for them to fully initialize
3. Refresh page (press F5)
4. If still red, check F12 Console for exact error

### Module Says "Save Failed"
1. Make sure all form fields are filled
2. Check browser F12 Console for error message
3. Verify MySQL is GREEN in XAMPP
4. Try refreshing page (F5)

---

## **Emergency: Everything's Broken?**

```
1. Close browser completely
2. Open XAMPP Control Panel  
3. Click [Stop] for Apache
4. Click [Stop] for MySQL
5. Wait 5 seconds
6. Click [Start] for Apache (wait for GREEN)
7. Click [Start] for MySQL (wait for GREEN)
8. Open browser again
9. Go to: http://localhost/scheduling_system/test_connection.php
10. Should now show ✅ "Connected!"
```

If still broken after that:
1. Restart your computer
2. Then repeat steps above

---

## **Quick Reference: URLs**

| Page | URL |
|------|-----|
| **Home** | http://localhost/scheduling_system/START.html |
| **Module 1** | http://localhost/scheduling_system/module1.html |
| **Module 2** | http://localhost/scheduling_system/module2.html |
| **Module 3** | http://localhost/scheduling_system/module3.html |
| **Module 4** | http://localhost/scheduling_system/module4.html |
| **Module 5** | http://localhost/scheduling_system/module5.html |
| **Database Test** | http://localhost/scheduling_system/test_connection.php |
| **phpMyAdmin** | http://localhost/phpmyadmin |

---

## 🎯 **GO! START WITH THIS:**

### 👉 **Just double-click AUTOSTART.bat!** 👈

That's all you need to do! Everything else happens automatically! 🚀
