<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Clearance.php';
require_once __DIR__ . '/../models/Employee.php';

use HR8\Config\Auth;
use HR8\Models\Clearance;
use HR8\Models\Employee;

class ClearanceController
{
    private Clearance $clearance;
    private Employee $employee;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'HR Manager', 'HR Staff', 'Department Head']);
        $this->clearance = new Clearance();
        $this->employee = new Employee();
    }

    public function index(array $filters = []): array
    {
        return [
            'clearances' => $this->clearance->getAllWithDetails($filters),
            'filters' => $filters,
        ];
    }

    public function show(int $id): array
    {
        $clearance = $this->clearance->getWithFullDetails($id);
        if (!$clearance) throw new RuntimeException('Clearance not found');
        return [
            'clearance' => $clearance,
            'signatories' => $this->clearance->getSignatories($id),
        ];
    }

    public function store(array $data): int|false
    {
        $data['requested_by'] = Auth::getUserId();
        $data['status'] = 'Pending';

        $id = $this->clearance->create($data);
        if ($id) {
            $this->clearance->initializeSignatories($id);
            log_audit($this->clearance->getDb(), 'Post-Employment', 'Created Clearance', 'clearance', $id, null, $data);
        }
        return $id;
    }

    public function updateSignatory(int $signatoryId, array $data): bool
    {
        if ($data['status'] === 'Cleared') {
            $data['signed_at'] = date('Y-m-d H:i:s');
        }
        $result = $this->clearance->updateSignatory($signatoryId, $data);
        if ($result) {
            log_audit($this->clearance->getDb(), 'Post-Employment', 'Updated Signatory', 'signatory', $signatoryId, null, $data);
        }
        return $result;
    }

    public function updateClearance(int $id, array $data): bool
    {
        $old = $this->clearance->find($id);
        $result = $this->clearance->update($id, $data);
        if ($result) {
            // If completed, update employee status
            if (isset($data['status']) && $data['status'] === 'Completed') {
                $clearance = $this->clearance->find($id);
                $emp = $this->employee->find((int)$clearance['employee_id']);
                $statusMap = [
                    'Resignation' => 'Resigned', 'Retirement' => 'Retired',
                    'Termination' => 'Terminated', 'End of Contract' => 'Resigned',
                    'AWOL' => 'Terminated',
                ];
                $newStatus = $statusMap[$clearance['separation_type']] ?? 'Resigned';
                $this->employee->update((int)$clearance['employee_id'], ['employment_status' => $newStatus]);
            }
            log_audit($this->clearance->getDb(), 'Post-Employment', 'Updated Clearance', 'clearance', $id, $old, $data);
        }
        return $result;
    }

    public function saveExitInterview(int $id, array $data): bool
    {
        $data['exit_interview_done'] = 1;
        $data['exit_interview_date'] = date('Y-m-d H:i:s');
        return $this->updateClearance($id, $data);
    }

    public function getActiveEmployees(): array
    {
        return $this->employee->getAllWithDetails(['status' => 'Active']);
    }
}
