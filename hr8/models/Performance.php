<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class Performance extends BaseModel
{
    protected string $table = 'performance_evaluations';
    protected string $primaryKey = 'eval_id';

    public function getAllEvaluations(array $filters = []): array
    {
        $sql = "SELECT pe.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, d.department_name, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as evaluator_name
                FROM performance_evaluations pe
                JOIN employees e ON pe.employee_id = e.employee_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN users u ON pe.evaluator_id = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['employee_id'])) {
            $sql .= " AND pe.employee_id = :emp_id";
            $params[':emp_id'] = $filters['employee_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND pe.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['period'])) {
            $sql .= " AND pe.evaluation_period LIKE :period";
            $params[':period'] = '%' . $filters['period'] . '%';
        }
        $sql .= " ORDER BY pe.evaluation_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEvalWithDetails(int $id): ?array
    {
        $sql = "SELECT pe.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, e.email, d.department_name, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as evaluator_name
                FROM performance_evaluations pe
                JOIN employees e ON pe.employee_id = e.employee_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN users u ON pe.evaluator_id = u.user_id
                WHERE pe.eval_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ---- Disciplinary Records ----
    public function getAllDisciplinary(array $filters = []): array
    {
        $sql = "SELECT dr.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, CONCAT(u.first_name, ' ', u.last_name) as issued_by_name
                FROM disciplinary_records dr
                JOIN employees e ON dr.employee_id = e.employee_id
                LEFT JOIN users u ON dr.issued_by = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['employee_id'])) {
            $sql .= " AND dr.employee_id = :emp_id";
            $params[':emp_id'] = $filters['employee_id'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND dr.type = :type";
            $params[':type'] = $filters['type'];
        }
        $sql .= " ORDER BY dr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createDisciplinary(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO disciplinary_records ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getDisciplinaryRecord(int $id): ?array
    {
        $sql = "SELECT dr.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, CONCAT(u.first_name, ' ', u.last_name) as issued_by_name
                FROM disciplinary_records dr
                JOIN employees e ON dr.employee_id = e.employee_id
                LEFT JOIN users u ON dr.issued_by = u.user_id
                WHERE dr.record_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
