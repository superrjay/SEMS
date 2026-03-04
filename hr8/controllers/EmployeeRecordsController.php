<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Employee.php';
require_once __DIR__ . '/../models/JobPosition.php';

use HR8\Config\Auth;
use HR8\Config\Database;
use HR8\Models\Employee;
use HR8\Models\JobPosition;
use HR8\Models\Department;

class EmployeeRecordsController
{
    private Employee $employee;
    private JobPosition $position;
    private Department $department;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'HR Manager', 'HR Staff']);
        $this->employee = new Employee();
        $this->position = new JobPosition();
        $this->department = new Department();
    }

    public function index(array $filters = []): array
    {
        return [
            'employees' => $this->employee->getAllWithDetails($filters),
            'departments' => $this->department->getActive(),
            'status_counts' => $this->employee->getStatusCounts(),
            'filters' => $filters,
        ];
    }

    public function show(int $id): array
    {
        $emp = $this->employee->getWithFullDetails($id);
        if (!$emp) throw new RuntimeException('Employee not found');
        return [
            'employee' => $emp,
            'documents' => $this->employee->getDocuments($id),
            'onboarding_tasks' => $this->employee->getOnboardingTasks($id),
        ];
    }

    public function store(array $data): int|false
    {
        $data['employee_no'] = $this->employee->generateEmployeeNo();
        $id = $this->employee->create($data);
        if ($id) {
            // Create default onboarding tasks
            $defaultTasks = [
                ['task_name' => 'Submit 201 File Documents', 'category' => 'Documentation'],
                ['task_name' => 'IT Account Setup (Email, System Access)', 'category' => 'IT Setup'],
                ['task_name' => 'Company Orientation', 'category' => 'Orientation'],
                ['task_name' => 'Department Orientation', 'category' => 'Orientation'],
                ['task_name' => 'Sign Employment Contract', 'category' => 'Documentation'],
                ['task_name' => 'Submit Government IDs (SSS, PhilHealth, Pag-IBIG, TIN)', 'category' => 'Compliance'],
                ['task_name' => 'Complete Safety & Policy Training', 'category' => 'Training'],
            ];
            foreach ($defaultTasks as $task) {
                $this->employee->addOnboardingTask([
                    'employee_id' => $id,
                    'task_name' => $task['task_name'],
                    'category' => $task['category'],
                    'status' => 'Pending',
                    'due_date' => date('Y-m-d', strtotime('+14 days')),
                ]);
            }
            log_audit($this->employee->getDb(), 'Employee Records', 'Created Employee', 'employee', $id, null, $data);
        }
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $old = $this->employee->find($id);
        $result = $this->employee->update($id, $data);
        if ($result) {
            log_audit($this->employee->getDb(), 'Employee Records', 'Updated Employee', 'employee', $id, $old, $data);
        }
        return $result;
    }

    public function updateOnboardingTask(int $taskId, string $status): bool
    {
        $data = ['status' => $status];
        if ($status === 'Completed') $data['completed_at'] = date('Y-m-d H:i:s');
        return $this->employee->updateOnboardingTask($taskId, $data);
    }

    public function addOnboardingTask(int $empId, array $data): int|false
    {
        $data['employee_id'] = $empId;
        return $this->employee->addOnboardingTask($data);
    }

    public function getPositions(): array { return $this->position->getActive(); }
    public function getDepartments(): array { return $this->department->getActive(); }
    public function getUsers(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT user_id, first_name, last_name FROM users WHERE status = 'active' ORDER BY last_name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
