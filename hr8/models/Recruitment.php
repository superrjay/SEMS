<?php
declare(strict_types=1);
namespace HR8\Models;

use PDO;

class Recruitment extends BaseModel
{
    protected string $table = 'interview_schedules';
    protected string $primaryKey = 'interview_id';

    // ---- Interviews ----
    public function getAllInterviews(array $filters = []): array
    {
        $sql = "SELECT i.*, CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
                       a.reference_no, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
                FROM interview_schedules i
                JOIN applicants a ON i.applicant_id = a.applicant_id
                LEFT JOIN job_positions jp ON a.position_applied_id = jp.position_id
                LEFT JOIN users u ON i.interviewer_id = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND i.status = :status";
            $params[':status'] = $filters['status'];
        }
        if (!empty($filters['applicant_id'])) {
            $sql .= " AND i.applicant_id = :applicant_id";
            $params[':applicant_id'] = $filters['applicant_id'];
        }
        $sql .= " ORDER BY i.interview_date DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInterviewWithDetails(int $id): ?array
    {
        $sql = "SELECT i.*, CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
                       a.reference_no, a.email as applicant_email, a.phone as applicant_phone,
                       jp.title as position_title, d.department_name,
                       CONCAT(u.first_name, ' ', u.last_name) as interviewer_name
                FROM interview_schedules i
                JOIN applicants a ON i.applicant_id = a.applicant_id
                LEFT JOIN job_positions jp ON a.position_applied_id = jp.position_id
                LEFT JOIN departments d ON jp.department_id = d.department_id
                LEFT JOIN users u ON i.interviewer_id = u.user_id
                WHERE i.interview_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // ---- Evaluations ----
    public function getEvaluations(int $interviewId): array
    {
        $sql = "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as evaluator_name
                FROM interview_evaluations e
                LEFT JOIN users u ON e.evaluator_id = u.user_id
                WHERE e.interview_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $interviewId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createEvaluation(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO interview_evaluations ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    // ---- Job Offers ----
    public function getAllOffers(array $filters = []): array
    {
        $sql = "SELECT o.*, CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
                       a.reference_no, jp.title as position_title,
                       CONCAT(u.first_name, ' ', u.last_name) as created_by_name
                FROM job_offers o
                JOIN applicants a ON o.applicant_id = a.applicant_id
                JOIN job_positions jp ON o.position_id = jp.position_id
                LEFT JOIN users u ON o.created_by = u.user_id
                WHERE 1=1";
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= " AND o.status = :status";
            $params[':status'] = $filters['status'];
        }
        $sql .= " ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createOffer(array $data): int|false
    {
        $cols = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $stmt = $this->db->prepare("INSERT INTO job_offers ({$cols}) VALUES ({$placeholders})");
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function updateOffer(int $id, array $data): bool
    {
        $updates = [];
        foreach (array_keys($data) as $key) {
            $updates[] = "{$key} = :{$key}";
        }
        $sql = "UPDATE job_offers SET " . implode(', ', $updates) . " WHERE offer_id = :pk_id";
        $data['pk_id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    public function getOffer(int $id): ?array
    {
        $sql = "SELECT o.*, CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
                       a.reference_no, a.email as applicant_email,
                       jp.title as position_title, d.department_name
                FROM job_offers o
                JOIN applicants a ON o.applicant_id = a.applicant_id
                JOIN job_positions jp ON o.position_id = jp.position_id
                LEFT JOIN departments d ON jp.department_id = d.department_id
                WHERE o.offer_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function getApplicantRanking(int $positionId): array
    {
        $sql = "SELECT a.applicant_id, CONCAT(a.first_name, ' ', a.last_name) as applicant_name,
                       a.reference_no, a.status,
                       AVG(ie.overall_score) as avg_score,
                       MAX(ie.recommendation) as recommendation
                FROM applicants a
                LEFT JOIN interview_schedules isch ON a.applicant_id = isch.applicant_id
                LEFT JOIN interview_evaluations ie ON isch.interview_id = ie.interview_id
                WHERE a.position_applied_id = :pos_id
                AND a.status NOT IN ('Rejected', 'Withdrawn', 'New')
                GROUP BY a.applicant_id
                ORDER BY avg_score DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pos_id' => $positionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
