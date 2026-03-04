<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class Clearance extends BaseModel
{
    protected string $table = 'clearance_requests';
    protected string $primaryKey = 'clearance_id';

    public function getAllWithDetails(array $filters = []): array
    {
        $sql = "SELECT cr.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, d.department_name, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as approved_by_name
                FROM clearance_requests cr
                JOIN employees e ON cr.employee_id = e.employee_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN users u ON cr.approved_by = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND cr.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $sql .= " AND cr.separation_type = :type";
            $params[':type'] = $filters['type'];
        }
        $sql .= " ORDER BY cr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWithFullDetails(int $id): ?array
    {
        $sql = "SELECT cr.*, CONCAT(e.first_name, ' ', e.last_name) as employee_name,
                       e.employee_no, e.email, e.phone, e.date_hired,
                       d.department_name, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as approved_by_name,
                       CONCAT(u2.first_name, ' ', u2.last_name) as requested_by_name
                FROM clearance_requests cr
                JOIN employees e ON cr.employee_id = e.employee_id
                LEFT JOIN departments d ON e.department_id = d.department_id
                LEFT JOIN job_positions jp ON e.position_id = jp.position_id
                LEFT JOIN users u ON cr.approved_by = u.user_id
                LEFT JOIN users u2 ON cr.requested_by = u2.user_id
                WHERE cr.clearance_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getSignatories(int $clearanceId): array
    {
        $sql = "SELECT cs.*, CONCAT(u.first_name, ' ', u.last_name) as signatory_name
                FROM clearance_signatories cs
                LEFT JOIN users u ON cs.signatory_user_id = u.user_id
                WHERE cs.clearance_id = :id ORDER BY cs.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $clearanceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addSignatory(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO clearance_signatories ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function updateSignatory(int $id, array $data): bool
    {
        $updates = [];
        foreach (array_keys($data) as $key) {
            $updates[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE clearance_signatories SET " . implode(', ', $updates) . " WHERE signatory_id = :pk_id";
        $data['pk_id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function initializeSignatories(int $clearanceId): void
    {
        $departments = ['Human Resources', 'Finance & Accounting', 'Information Technology', 'Administration', 'Library', 'Property & Supply'];
        foreach ($departments as $dept) {
            $this->addSignatory([
                'clearance_id' => $clearanceId,
                'department_name' => $dept,
                'status' => 'Pending'
            ]);
        }
    }
}
