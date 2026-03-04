<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/paths.php';
require_once __DIR__ . '/../includes/helpers.php';
require_once __DIR__ . '/../models/BaseModel.php';
require_once __DIR__ . '/../models/Recruitment.php';
require_once __DIR__ . '/../models/Applicant.php';
require_once __DIR__ . '/../models/JobPosition.php';

use HR8\Config\Auth;
use HR8\Config\Database;
use HR8\Models\Recruitment;
use HR8\Models\Applicant;
use HR8\Models\JobPosition;

class RecruitmentController
{
    private Recruitment $recruitment;
    private Applicant $applicant;
    private JobPosition $position;

    public function __construct()
    {
        Auth::requireRole(['Admin', 'HR Manager', 'HR Staff', 'Department Head']);
        $this->recruitment = new Recruitment();
        $this->applicant = new Applicant();
        $this->position = new JobPosition();
    }

    public function interviews(array $filters = []): array
    {
        return [
            'interviews' => $this->recruitment->getAllInterviews($filters),
            'filters' => $filters,
        ];
    }

    public function scheduleInterview(array $data): int|false
    {
        $id = $this->recruitment->create($data);
        if ($id) {
            $this->applicant->update((int)$data['applicant_id'], ['status' => 'For Interview']);
            log_audit($this->recruitment->getDb(), 'Recruitment', 'Scheduled Interview', 'interview', $id, null, $data);
        }
        return $id;
    }

    public function updateInterview(int $id, array $data): bool
    {
        $old = $this->recruitment->find($id);
        $result = $this->recruitment->update($id, $data);
        if ($result) {
            if (isset($data['status']) && $data['status'] === 'Completed') {
                $this->applicant->update((int)$old['applicant_id'], ['status' => 'Interviewed']);
            }
            log_audit($this->recruitment->getDb(), 'Recruitment', 'Updated Interview', 'interview', $id, $old, $data);
        }
        return $result;
    }

    public function submitEvaluation(array $data): int|false
    {
        $data['evaluator_id'] = Auth::getUserId();
        $scores = array_filter([$data['communication_score'] ?? 0, $data['technical_score'] ?? 0, $data['experience_score'] ?? 0, $data['cultural_fit_score'] ?? 0]);
        $data['overall_score'] = count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0;

        $id = $this->recruitment->createEvaluation($data);
        if ($id) {
            log_audit($this->recruitment->getDb(), 'Recruitment', 'Submitted Evaluation', 'evaluation', $id, null, $data);
        }
        return $id;
    }

    // ---- Job Offers ----
    public function offers(array $filters = []): array
    {
        return ['offers' => $this->recruitment->getAllOffers($filters)];
    }

    public function createOffer(array $data): int|false
    {
        $data['created_by'] = Auth::getUserId();
        $id = $this->recruitment->createOffer($data);
        if ($id) {
            $this->applicant->update((int)$data['applicant_id'], ['status' => 'Offered']);
            log_audit($this->recruitment->getDb(), 'Recruitment', 'Created Job Offer', 'offer', $id, null, $data);
        }
        return $id;
    }

    public function updateOfferStatus(int $id, string $status): bool
    {
        $old = $this->recruitment->getOffer($id);
        $result = $this->recruitment->updateOffer($id, ['status' => $status]);
        if ($result && $status === 'Accepted') {
            $this->applicant->update((int)$old['applicant_id'], ['status' => 'Hired']);
        }
        if ($result) {
            log_audit($this->recruitment->getDb(), 'Recruitment', "Offer {$status}", 'offer', $id, ['status' => $old['status']], ['status' => $status]);
        }
        return $result;
    }

    public function getRanking(int $positionId): array
    {
        return $this->recruitment->getApplicantRanking($positionId);
    }

    public function getApplicantsForInterview(): array
    {
        return $this->applicant->getAllWithPosition(['status' => 'Shortlisted']);
    }

    public function getUsers(): array
    {
        $db = Database::getConnection();
        $stmt = $db->query("SELECT user_id, first_name, last_name, email FROM users WHERE status = 'active' ORDER BY last_name");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
