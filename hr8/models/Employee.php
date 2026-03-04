<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class Employee extends BaseModel
{
    protected string $table = 'employees';
    protected string $primaryKey = 'employee_id';

    public function getAllWithDetails(array $filters = []): array
    {
        $sql = "SELECT e.*, jp.title as position_title, d.department_name
                FROM employees e
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                WHERE e.is_archived = 0";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND e.employment_status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['department_id'])) {
            $sql .= " AND e.department_id = :dept_id";
            $params[':dept_id'] = $filters['department_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (e.first_name LIKE :s1 OR e.last_name LIKE :s2 OR e.employee_no LIKE :s3 OR e.email LIKE :s4)";
            $s = '%' . $filters['search'] . '%';
            $params[':s1'] = $s; $params[':s2'] = $s; $params[':s3'] = $s; $params[':s4'] = $s;
        }
        $sql .= " ORDER BY e.last_name, e.first_name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWithFullDetails(int $id): ?array
    {
        $sql = "SELECT e.*, jp.title as position_title, d.department_name
                FROM employees e
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                WHERE e.employee_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getDocuments(int $employeeId): array
    {
        $stmt = $this->db->prepare("SELECT ed.*, CONCAT(u.first_name, ' ', u.last_name) as uploaded_by_name FROM employee_documents ed LEFT JOIN users u ON ed.uploaded_by = u.user_id WHERE ed.employee_id = :id ORDER BY ed.uploaded_at DESC");
        $stmt->execute([':id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDocument(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO employee_documents ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getOnboardingTasks(int $employeeId): array
    {
        $sql = "SELECT t.*, CONCAT(u.first_name, ' ', u.last_name) as assigned_to_name
                FROM onboarding_tasks t
                LEFT JOIN users u ON t.assigned_to = u.user_id
                WHERE t.employee_id = :id ORDER BY t.due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $employeeId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addOnboardingTask(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO onboarding_tasks ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function updateOnboardingTask(int $id, array $data): bool
    {
        $updates = [];
        foreach (array_keys($data) as $key) {
            $updates[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE onboarding_tasks SET " . implode(', ', $updates) . " WHERE task_id = :pk_id";
        $data['pk_id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function generateEmployeeNo(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT MAX(CAST(SUBSTRING(employee_no, -4) AS UNSIGNED)) as max_no FROM employees WHERE employee_no LIKE :prefix");
        $stmt->execute([':prefix' => "EMP-{$year}-%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = ($result['max_no'] ?? 0) + 1;
        return "EMP-{$year}-" . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusCounts(): array
    {
        $stmt = $this->db->query("SELECT employment_status, COUNT(*) as count FROM employees WHERE is_archived = 0 GROUP BY employment_status");
        $counts = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[$row['employment_status']] = (int)$row['count'];
        }
        return $counts;
    }

    public function getDepartmentCounts(): array
    {
        $stmt = $this->db->query("SELECT d.department_name, COUNT(e.employee_id) as count FROM departments d LEFT JOIN employees e ON d.department_id = e.department_id AND e.is_archived = 0 GROUP BY d.department_id ORDER BY count DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
