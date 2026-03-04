<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class Applicant extends BaseModel
{
    protected string $table = 'applicants';
    protected string $primaryKey = 'applicant_id';

    public function getAllWithPosition(array $filters = []): array
    {
        $sql = "SELECT a.*, jp.title as position_title, jp.department_id,
                       d.department_name
                FROM applicants a
                LEFT JOIN job_positions jp ON a.position_applied_id = jp.position_id
                LEFT JOIN departments d ON jp.department_id = d.department_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status'])) {
            $sql .= " AND a.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['position_id'])) {
            $sql .= " AND a.position_applied_id = :position_id";
            $params[':position_id'] = $filters['position_id'];
        }
        if (!empty($filters['search'])) {
            $sql .= " AND (a.first_name LIKE :search OR a.last_name LIKE :search2 OR a.email LIKE :search3 OR a.reference_no LIKE :search4)";
            $params[':search'] = '%' . $filters['search'] . '%';
            $params[':search2'] = '%' . $filters['search'] . '%';
            $params[':search3'] = '%' . $filters['search'] . '%';
            $params[':search4'] = '%' . $filters['search'] . '%';
        }
        $sql .= " ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWithDetails(int $id): ?array
    {
        $sql = "SELECT a.*, jp.title as position_title, d.department_name,
                       u.first_name as created_by_name
                FROM applicants a
                LEFT JOIN job_positions jp ON a.position_applied_id = jp.position_id
                LEFT JOIN departments d ON jp.department_id = d.department_id
                LEFT JOIN users u ON a.created_by = u.user_id
                WHERE a.applicant_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getDocuments(int $applicantId): array
    {
        $stmt = $this->db->prepare("SELECT * FROM applicant_documents WHERE applicant_id = :id ORDER BY uploaded_at DESC");
        $stmt->execute([':id' => $applicantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addDocument(array $data): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO applicant_documents (applicant_id, document_type, file_name, file_path) VALUES (:applicant_id, :document_type, :file_name, :file_path)");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function getScreenings(int $applicantId): array
    {
        $sql = "SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as screener_name
                FROM applicant_screenings s
                LEFT JOIN users u ON s.screened_by = u.user_id
                WHERE s.applicant_id = :id ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $applicantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function addScreening(array $data): int|false
    {
        $stmt = $this->db->prepare("INSERT INTO applicant_screenings (applicant_id, screened_by, screening_date, documents_complete, qualifications_met, remarks, result) VALUES (:applicant_id, :screened_by, :screening_date, :documents_complete, :qualifications_met, :remarks, :result)");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function generateReferenceNo(): string
    {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT MAX(CAST(SUBSTRING(reference_no, -4) AS UNSIGNED)) as max_no FROM applicants WHERE reference_no LIKE :prefix");
        $stmt->execute([':prefix' => "APP-{$year}-%"]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $next = ($result['max_no'] ?? 0) + 1;
        return "APP-{$year}-" . str_pad((string)$next, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusCounts(): array
    {
        $stmt = $this->db->query("SELECT status, COUNT(*) as count FROM applicants GROUP BY status");
        $counts = [];
        foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $counts[$row['status']] = (int)$row['count'];
        }
        return $counts;
    }
}
