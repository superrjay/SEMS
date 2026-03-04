# HR8 - Human Resource Management Sub-system
## Basic MVC Architecture | PHP 8.1+ | MySQL | Bootstrap 5

---

## System Modules

| # | Module | Description |
|---|--------|-------------|
| 1 | **Pre-Employment Management** | Applicant profiling, document submission, initial screening |
| 2 | **Recruitment & Selection Workflow** | Interviews, evaluations, ranking, job offers |
| 3 | **Employment Records & Onboarding** | Employee 201 files, contracts, onboarding tasks |
| 4 | **Employee Performance & Service Mgmt** | Performance evaluations, disciplinary records, commendations |
| 5 | **Post-Employment & Clearance Processing** | Resignation/retirement, exit interviews, clearance routing |
| 6 | **User Management & Audit Trail** | User accounts, roles, activity logs |

---

## Project Structure (MVC)

```
hr8/
├── index.php                          # Entry redirect
├── login.php                          # Authentication
├── logout.php                         # Session destroy
├── dashboard.php                      # Main dashboard with stats
│
├── config/
│   ├── db.php                         # Database connection (PDO singleton)
│   ├── auth.php                       # Auth & session management
│   └── paths.php                      # Path constants
│
├── models/                            # [M] Data layer
│   ├── BaseModel.php                  # Abstract CRUD base class
│   ├── Applicant.php                  # Pre-employment applicant model
│   ├── Recruitment.php                # Interviews, evaluations, offers
│   ├── Employee.php                   # Employee records & onboarding
│   ├── Performance.php                # Evaluations & disciplinary
│   ├── Clearance.php                  # Post-employment clearance
│   └── JobPosition.php                # Positions, departments, audit logs
│
├── controllers/                       # [C] Business logic
│   ├── PreEmploymentController.php    # Module 1 controller
│   ├── RecruitmentController.php      # Module 2 controller
│   ├── EmployeeRecordsController.php  # Module 3 controller
│   ├── PerformanceController.php      # Module 4 controller
│   └── ClearanceController.php        # Module 5 controller
│
├── modules/                           # [V] Views (per module)
│   ├── pre_employment/
│   │   ├── index.php                  # Applicant list, create, view, screen
│   │   └── positions.php              # Job position management
│   ├── recruitment/
│   │   ├── interviews.php             # Schedule, track, evaluate interviews
│   │   └── offers.php                 # Create & manage job offers
│   ├── employee_records/
│   │   ├── index.php                  # Employee list & detail view
│   │   └── create.php                 # New employee form (201 file)
│   ├── performance/
│   │   ├── index.php                  # Performance evaluations
│   │   └── disciplinary.php           # Disciplinary & commendation records
│   ├── post_employment/
│   │   └── index.php                  # Clearance processing & exit interviews
│   └── user_management/
│       ├── index.php                  # User account management
│       └── audit.php                  # Activity logs & audit trail
│
├── includes/                          # Shared components
│   ├── navbar.php                     # Top navigation bar
│   ├── sidebar.php                    # Sidebar with module navigation
│   └── helpers.php                    # Utility functions
│
├── sql/
│   └── hr8_db.sql                     # Complete database schema + seed data
│
├── uploads/                           # File storage
│   ├── applicant_docs/
│   ├── employee_docs/
│   ├── performance_docs/
│   └── clearance_docs/
│
└── logs/                              # Error logs
```

---

## Installation

1. **Database Setup:**
   - Create database `hr8_db` in MySQL/MariaDB
   - Import `sql/hr8_db.sql`

2. **Configure:**
   - Edit `config/db.php` (host, user, pass if needed)
   - Place project in web server root as `/hr8`

3. **Setup Passwords:**
   - Visit `http://localhost/hr8/setup_admin.php` (auto-sets all passwords to `admin123`)
   - **Delete `setup_admin.php` after setup!**

4. **Access:**
   - URL: `http://localhost/hr8`
   - Login: `admin@hr8.com` / `admin123`

---

## User Roles

| Role | Access |
|------|--------|
| Admin | Full system access |
| HR Manager | HR management & approvals |
| HR Staff | HR operations & processing |
| Department Head | Department-level reviews |
| Employee | Self-service (limited) |

---

## Database Tables (16 tables)

**Core:** `roles`, `departments`, `users`
**Module 1:** `job_positions`, `applicants`, `applicant_documents`, `applicant_screenings`
**Module 2:** `interview_schedules`, `interview_evaluations`, `job_offers`
**Module 3:** `employees`, `employee_documents`, `onboarding_tasks`
**Module 4:** `performance_evaluations`, `disciplinary_records`
**Module 5:** `clearance_requests`, `clearance_signatories`
**Module 6:** `audit_logs`

---

## Key Features

- **CSRF Protection** on all forms
- **Role-based access control** (RBAC)
- **Audit trail** for all critical actions
- **Responsive design** (Bootstrap 5)
- **PDO prepared statements** (SQL injection protection)
- **Session timeout** (30 minutes)
- **Auto-generated** reference numbers & employee IDs
- **Clearance routing** with multi-department signatories
- **Performance scoring** with auto-calculated grades
