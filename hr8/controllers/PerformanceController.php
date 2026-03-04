<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Performance.php';
require_once __DIR__ . '/../models/Employee.php';

use HR8\Config\Auth;
use HR8\Models\Performance;
use HR8\Models\Employee;

class PerformanceController
{
    private Performance $performance;
    private Employee $employee;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'HR Manager', 'HR Staff', 'Department Head']);
        $this->performance = new Performance();
        $this->employee = new Employee();
    }

    public function evaluations(array $filters = []): array
    {
        return [
            'evaluations' => $this->performance->getAllEvaluations($filters),
            'filters' => $filters,
        ];
    }

    public function showEvaluation(int $id): ?array
    {
        return $this->performance->getEvalWithDetails($id);
    }

    public function createEvaluation(array $data): int|false
    {
        $data['evaluator_id'] = Auth::getUserId();
        // Calculate overall
        $scores = array_filter([
            (int)($data['job_knowledge'] ?? 0), (int)($data['work_quality'] ?? 0),
            (int)($data['productivity'] ?? 0), (int)($data['communication'] ?? 0),
            (int)($data['teamwork'] ?? 0), (int)($data['attendance'] ?? 0),
            (int)($data['initiative'] ?? 0)
        ]);
        $avg = count($scores) > 0 ? array_sum($scores) / count($scores) : 0;
        $data['overall_rating'] = round($avg, 2);

        if ($avg >= 4.5) $data['overall_grade'] = 'Outstanding';
        elseif ($avg >= 3.5) $data['overall_grade'] = 'Very Satisfactory';
        elseif ($avg >= 2.5) $data['overall_grade'] = 'Satisfactory';
        elseif ($avg >= 1.5) $data['overall_grade'] = 'Needs Improvement';
        else $data['overall_grade'] = 'Unsatisfactory';

        $id = $this->performance->create($data);
        if ($id) {
            log_audit($this->performance->getDb(), 'Performance', 'Created Evaluation', 'evaluation', $id, null, $data);
        }
        return $id;
    }

    public function updateEvaluation(int $id, array $data): bool
    {
        $old = $this->performance->find($id);
        $result = $this->performance->update($id, $data);
        if ($result) {
            log_audit($this->performance->getDb(), 'Performance', 'Updated Evaluation', 'evaluation', $id, $old, $data);
        }
        return $result;
    }

    // ---- Disciplinary ----
    public function disciplinaryRecords(array $filters = []): array
    {
        return ['records' => $this->performance->getAllDisciplinary($filters)];
    }

    public function createDisciplinary(array $data): int|false
    {
        $data['issued_by'] = Auth::getUserId();
        $id = $this->performance->createDisciplinary($data);
        if ($id) {
            log_audit($this->performance->getDb(), 'Performance', 'Created Disciplinary Record', 'disciplinary', $id, null, $data);
        }
        return $id;
    }

    public function getEmployees(): array
    {
        return $this->employee->getAllWithDetails();
    }
}
