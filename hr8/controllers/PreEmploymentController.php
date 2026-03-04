<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Applicant.php';
require_once __DIR__ . '/../models/JobPosition.php';

use HR8\Config\Auth;
use HR8\Models\Applicant;
use HR8\Models\JobPosition;
use HR8\Models\Department;

class PreEmploymentController
{
    private Applicant $applicant;
    private JobPosition $position;
    private Department $department;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'HR Manager', 'HR Staff']);
        $this->applicant = new Applicant();
        $this->position = new JobPosition();
        $this->department = new Department();
    }

    public function index(array $filters = []): array
    {
        return [
            'applicants' => $this->applicant->getAllWithPosition($filters),
            'positions' => $this->position->getActive(),
            'status_counts' => $this->applicant->getStatusCounts(),
            'filters' => $filters,
        ];
    }

    public function show(int $id): array
    {
        $applicant = $this->applicant->getWithDetails($id);
        if (!$applicant) throw new RuntimeException('Applicant not found');
        return [
            'applicant' => $applicant,
            'documents' => $this->applicant->getDocuments($id),
            'screenings' => $this->applicant->getScreenings($id),
        ];
    }

    public function store(array $data): int|false
    {
        $data['reference_no'] = $this->applicant->generateReferenceNo();
        $data['created_by'] = Auth::getUserId();
        $data['status'] = 'New';

        $id = $this->applicant->create($data);
        if ($id) {
            log_audit($this->applicant->getDb(), 'Pre-Employment', 'Created Applicant', 'applicant', $id, null, $data);
        }
        return $id;
    }

    public function update(int $id, array $data): bool
    {
        $old = $this->applicant->find($id);
        $result = $this->applicant->update($id, $data);
        if ($result) {
            log_audit($this->applicant->getDb(), 'Pre-Employment', 'Updated Applicant', 'applicant', $id, $old, $data);
        }
        return $result;
    }

    public function updateStatus(int $id, string $status): bool
    {
        $old = $this->applicant->find($id);
        $result = $this->applicant->update($id, ['status' => $status]);
        if ($result) {
            log_audit($this->applicant->getDb(), 'Pre-Employment', "Status changed to {$status}", 'applicant', $id, ['status' => $old['status']], ['status' => $status]);
        }
        return $result;
    }

    public function screen(int $applicantId, array $data): int|false
    {
        $data['applicant_id'] = $applicantId;
        $data['screened_by'] = Auth::getUserId();
        $id = $this->applicant->addScreening($data);
        if ($id) {
            $newStatus = $data['result'] === 'Passed' ? 'Shortlisted' : ($data['result'] === 'Failed' ? 'Rejected' : 'Screening');
            $this->applicant->update($applicantId, ['status' => $newStatus]);
            log_audit($this->applicant->getDb(), 'Pre-Employment', 'Screened Applicant', 'applicant', $applicantId, null, $data);
        }
        return $id;
    }

    public function getPositions(): array { return $this->position->getActive(); }
    public function getDepartments(): array { return $this->department->getActive(); }
}
