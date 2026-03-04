# Database Integration Guide - Complete System

## Overview
Your scheduling system now integrates with the centralized `enrollment_system` database across all 5 modules:

1. **Module 1** - Section Creation & Assignment
2. **Module 2** - Class Timetable Generation  
3. **Module 3** - Room Assignment & Validation
4. **Module 4** - Teacher Loading Management
5. **Module 5** - System Conflict Scanner

## Setup Instructions

### 1. Database Configuration
All API files expect the same MySQL database credentials:
```php
$servername = "localhost";
$db_user = "root";          // Change if needed
$db_password = "";          // Add password if needed
$dbname = "enrollment_system";
```

### 2. Create the Database
1. Open phpMyAdmin or MySQL command line
2. Run the database schema script (the SQL file you provided)
3. Verify the `enrollment_system` database is created with all tables

### 3. Add Sample Data (CRITICAL!)

**Add Sample Faculty:**
```sql
INSERT INTO employees (employee_number, first_name, last_name, employee_type, employment_status, POSITION) VALUES
('EMP001', 'Juan', 'Dela Cruz', 'Faculty', 'Regular', 'Instructor'),
('EMP002', 'Maria', 'Santos', 'Faculty', 'Regular', 'Assistant Professor'),
('EMP003', 'Jose', 'Reyes', 'Faculty', 'Probationary', 'Instructor'),
('EMP004', 'Ana', 'Garcia', 'Faculty', 'Regular', 'Associate Professor');
```

**Add Subjects:**
```sql
INSERT INTO subjects (subject_code, subject_name, subject_type, units) VALUES
('CS101', 'Introduction to Computing', 'Major', 3),
('CS102', 'Data Structures', 'Major', 3),
('CS103', 'Database Management', 'Major', 4),
('GE101', 'English Composition', 'GE', 3),
('PE101', 'Physical Education', 'PE', 2),
('MATH101', 'Calculus I', 'Major', 4);
```

**Add Rooms:**
```sql
INSERT INTO rooms (room_number, room_name, building, capacity, room_type) VALUES
('RM201', 'Computer Lab A', 'Engineering Building', 40, 'Laboratory'),
('RM202', 'Lecture Hall 1', 'Main Building', 60, 'Classroom'),
('RM203', 'Science Lab', 'Science Building', 35, 'Laboratory'),
('RM204', 'Conference Room', 'Admin Building', 30, 'Classroom'),
('RM205', 'Computer Lab B', 'Engineering Building', 45, 'Laboratory');
```

**Add Enrollment Period (REQUIRED!):**
```sql
INSERT INTO enrollment_periods (school_year, semester, start_date, end_date, is_active) VALUES
('2025-2026', '1st Semester', '2025-08-01', '2025-12-31', TRUE),
('2025-2026', '2nd Semester', '2026-01-01', '2026-05-31', FALSE),
('2025-2026', 'Summer', '2026-06-01', '2026-07-31', FALSE);
```

### 4. File Structure
```
scheduling_system/
├── index.html
├── module1.html          (Section Creation - Updated)
├── module2.html          (Timetable Generation - Updated; grid cells are clickable and dropdown auto-loads existing schedule)
├── module3.html          (Room Assignment - Updated)
├── module4.html          (Teacher Loading - Updated)
├── module5.html          (Conflict Scanner - Updated)
├── api_section.php       (Section API)
├── api_schedule.php      (Schedule/Room/Teacher API)
└── DATABASE_SETUP.md     (This file)
```

### 5. API Endpoints

#### api_section.php - Section Management
```
GET  /api_section.php?action=getSubjects   → List all subjects
GET  /api_section.php?action=getSections   → List all sections
POST /api_section.php → Save new section (action=saveSection)
```

#### api_schedule.php - Full Schedule Management
```
GET  /api_schedule.php?action=getSections     → All sections with details
GET  /api_schedule.php?action=getSchedules    → Sections with schedule/room/teacher
GET  /api_schedule.php?action=getRooms        → Available rooms
GET  /api_schedule.php?action=getFaculty      → Available faculty
GET  /api_schedule.php?action=getConflicts    → Detected conflicts

POST /api_schedule.php → assignSchedule       → Set timetable for section
POST /api_schedule.php → assignRoom           → Assign room to section
POST /api_schedule.php → assignTeacher        → Assign faculty to section
POST /api_schedule.php → updateSchedule       → Update schedule details
```

### 6. Module Workflow

**Module 1 - Section Creation**
- Create new sections with subject assignment
- Stores in `sections` table
- Pulls subjects from `subjects` table

**Module 2 - Timetable Assignment**
- Ang module ay gumagamit ng automated algorithm na kumukuha ng sections mula sa Module 1
- Ang system ang nag-a-assign ng optimal time slots para sa bawat subject upang ma-maximize ang paggamit ng classrooms at maiwasan ang scheduling conflicts ng guro at estudyante
- Manual cell clicking ay disabled; ang lahat ng scheduling ay ginagawa ng program
- Nag-uupdate ng `sections.schedule_days`, `time_start`, `time_end` gamit ang API

**Module 3 - Room Assignment**
- Select plotted sections from Module 2
- Assign classroom/lab to section
- Updates `sections.room_id` with room details

**Module 4 - Teacher Loading**
- Select sections from Module 2
- Search for faculty by name
- Updates `sections.faculty_id` with teacher assignment
- Shows live faculty loading monitor

**Module 5 - Conflict Scanning**
- Runs audit on all sections
- Detects room double-booking (same room, day, time)
- Detects faculty overlap (same teacher, day, time)
- Displays real-time conflicts from database

### 7. Test the Connection

1. **Start XAMPP** (Apache + MySQL both running)

2. **Open Module 1**
   ```
   http://localhost/scheduling_system/module1.html
   ```
   - Should load subjects from database dropdown
   - Create a test section

3. **Open Module 2**
   ```
   http://localhost/scheduling_system/module2.html
   ```
   - Should show section you created
   - Click timetable cell to assign schedule

4. **Open Module 3**
   ```
   http://localhost/scheduling_system/module3.html
   ```
   - Should show your section with schedule
   - Assign a room

5. **Open Module 4**
   ```
   http://localhost/scheduling_system/module4.html
   ```
   - Should show your section
   - Assign a faculty member

6. **Open Module 5**
   ```
   http://localhost/scheduling_system/module5.html
   ```
   - Click "RUN FINAL SYSTEM AUDIT"
   - Should report no conflicts (or show any detected)

### 8. Troubleshooting

**Error: "Database connection failed"**
- XAMPP MySQL service not running
- Check credentials in API files
- Verify database name is `enrollment_system`

**Error: "Subject not found"**
- Need to add subjects to database
- Run INSERT statements from section 3
- Dropdown should populate automatically

**Error: "No active enrollment period"**
- Must create enrollment period with `is_active = TRUE`
- Use INSERT statement from section 3
- Module 1 cannot save sections without active period

**Module showing "Loading..." forever**
- Check browser console for errors (F12)
- Verify XAMPP is running
- Check API file paths are correct
- Ensure database is accessible

**Sections not appearing in Module 2**
- Module 1 must be used to create sections first
- Database must have active enrollment period
- Check sections table in phpMyAdmin

**No conflicts detected in Module 5**
- This is normal if no actual conflicts exist
- Try assigning same room to 2 sections at same time
- Try assigning same teacher to 2 sections at same time

### 9. Security Notes (Production)
For production deployment:
- Move database credentials to config file outside web root
- Add authentication/authorization checks
- Validate and sanitize all user inputs
- Use HTTPS only
- Set up proper user permissions in MySQL
- Add rate limiting
- Implement CORS properly
- Add request logging/auditing

