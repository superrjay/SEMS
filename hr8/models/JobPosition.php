<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class JobPosition extends BaseModel
{
    protected string $table = 'job_positions';
    protected string $primaryKey = 'position_id';

    public function getAllWithDepartment(): array
    {
        $sql = "SELECT jp.*, d.department_name, 
                       (SELECT COUNT(*) FROM applicants a WHERE a.position_applied_id = jp.position_id AND a.status NOT IN ('Rejected','Withdrawn','Hired')) as active_applicants
                FROM job_positions jp
                LEFT JOIN departments d ON jp.department_id = d.department_id
                ORDER BY jp.title";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getActive(): array
    {
        return $this->all(['is_active' => 1], 'title ASC');
    }
}

class Department extends BaseModel
{
    protected string $table = 'departments';
    protected string $primaryKey = 'department_id';

    public function getActive(): array
    {
        return $this->all(['is_active' => 1], 'department_name ASC');
    }
}

class AuditLog extends BaseModel
{
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'log_id';

    public function getAll(array $filters = [], int $limit = 100): array
    {
        $sql = "SELECT al.*, CONCAT(u.first_name, ' ', u.last_name) as user_name, u.email
                FROM audit_logs al
                LEFT JOIN users u ON al.user_id = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['module'])) {
            $sql .= " AND al.module = :module";
            $params[':module'] = $filters['module'];
        }
        if (!empty($filters['user_id'])) {
            $sql .= " AND al.user_id = :user_id";
            $params[':user_id'] = $filters['user_id'];
        }
        if (!empty($filters['date_from'])) {
            $sql .= " AND DATE(al.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $sql .= " AND DATE(al.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (al.action LIKE :s1 OR al.module LIKE :s2)";
            $s = '%' . $filters['search'] . '%';
            $params[':s1'] = $s; $params[':s2'] = $s;
        }
        $sql .= " ORDER BY al.created_at DESC LIMIT {$limit}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getModules(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT module FROM audit_logs ORDER BY module");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
