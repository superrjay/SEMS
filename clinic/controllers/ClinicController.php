<?php
declare(strict_types=1);
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/auth.php';
require_once __DIR__.'/../config/paths.php';
require_once __DIR__.'/../includes/helpers.php';
require_once __DIR__.'/../models/BaseModel.php';
require_once __DIR__.'/../models/Clinic.php';

use Clinic\Config\Auth;
use Clinic\Config\Database;
use Clinic\Models\{Patient, MedicalRecord, Consultation, Medicine, MedicineDispensing, MedicalClearance, HealthIncident, AuditLog};

Auth::requireAuth();

class ClinicController {
    private Patient $patient;
    private MedicalRecord $record;
    private Consultation $consultation;
    private Medicine $medicine;
    private MedicineDispensing $dispensing;
    private MedicalClearance $clearance;
    private HealthIncident $incident;
    private \PDO $db;

    public function __construct() {
        $this->patient = new Patient();
        $this->record = new MedicalRecord();
        $this->consultation = new Consultation();
        $this->medicine = new Medicine();
        $this->dispensing = new MedicineDispensing();
        $this->clearance = new MedicalClearance();
        $this->incident = new HealthIncident();
        $this->db = Database::getConnection();
    }

    // Patients
    public function getPatients(array $f=[]): array { return $this->patient->search($f); }
    public function getPatient(int $id): ?array { return $this->patient->find($id); }
    public function storePatient(array $d): int|false { $id=$this->patient->create($d); if($id)log_audit($this->db,'Medical Records','Registered Patient','patient',$id); return $id; }
    public function updatePatient(int $id, array $d): bool { $r=$this->patient->update($id,$d); if($r)log_audit($this->db,'Medical Records','Updated Patient','patient',$id); return $r; }
    public function getAllPatients(): array { return $this->patient->all([],'last_name ASC'); }

    // Medical Records
    public function getMedicalRecords(int $pid): array { return $this->record->getByPatient($pid); }
    public function addMedicalRecord(array $d): int|false { $d['recorded_by']=Auth::getUserId(); $id=$this->record->create($d); if($id)log_audit($this->db,'Medical Records','Added Record','medical_record',$id); return $id; }

    // Consultations
    public function getConsultations(array $f=[]): array { return $this->consultation->getAllWithPatient($f); }
    public function getConsultation(int $id): ?array { return $this->consultation->getWithDetails($id); }
    public function storeConsultation(array $d): int|false { $id=$this->consultation->create($d); if($id)log_audit($this->db,'Consultations','Created Consultation','consultation',$id); return $id; }
    public function updateConsultation(int $id, array $d): bool { $r=$this->consultation->update($id,$d); if($r)log_audit($this->db,'Consultations','Updated Consultation','consultation',$id); return $r; }

    // Medicine
    public function getMedicines(): array { $this->medicine->refreshStatuses(); return $this->medicine->all([],'name ASC'); }
    public function getAvailableMedicines(): array { return $this->medicine->getAvailable(); }
    public function storeMedicine(array $d): int|false { $id=$this->medicine->create($d); if($id)log_audit($this->db,'Medicine','Added Medicine','medicine',$id); return $id; }
    public function updateMedicine(int $id, array $d): bool { $r=$this->medicine->update($id,$d); if($r)log_audit($this->db,'Medicine','Updated Medicine','medicine',$id); return $r; }

    // Dispensing
    public function getDispensingLogs(): array { return $this->dispensing->getAllWithDetails(); }
    public function dispense(array $d): int|false {
        $d['dispensed_by']=Auth::getUserId(); $d['dispensed_date']=date('Y-m-d H:i:s');
        if(!$this->medicine->updateStock((int)$d['medicine_id'],(int)$d['quantity'])) return false;
        $id=$this->dispensing->create($d);
        if($id){$this->medicine->refreshStatuses(); log_audit($this->db,'Medicine','Dispensed Medicine','dispensing',$id);}
        return $id;
    }

    // Clearance
    public function getClearances(array $f=[]): array { return $this->clearance->getAllWithPatient($f); }
    public function storeClearance(array $d): int|false { $d['issued_by']=Auth::getUserId(); $id=$this->clearance->create($d); if($id)log_audit($this->db,'Clearance','Issued Clearance','clearance',$id); return $id; }
    public function updateClearance(int $id, array $d): bool { $r=$this->clearance->update($id,$d); if($r)log_audit($this->db,'Clearance','Updated Clearance','clearance',$id); return $r; }

    // Incidents
    public function getIncidents(array $f=[]): array { return $this->incident->getAllWithDetails($f); }
    public function storeIncident(array $d): int|false { $d['reported_by']=Auth::getUserId(); $id=$this->incident->create($d); if($id)log_audit($this->db,'Incidents','Reported Incident','incident',$id); return $id; }
    public function updateIncident(int $id, array $d): bool { $r=$this->incident->update($id,$d); if($r)log_audit($this->db,'Incidents','Updated Incident','incident',$id); return $r; }

    // Doctors list for dropdowns
    public function getDoctors(): array { return $this->db->query("SELECT user_id,CONCAT(first_name,' ',last_name) as name FROM users WHERE role_id IN (1,2) AND status='active' ORDER BY first_name")->fetchAll(\PDO::FETCH_ASSOC); }
    public function getNurses(): array { return $this->db->query("SELECT user_id,CONCAT(first_name,' ',last_name) as name FROM users WHERE role_id IN (1,3) AND status='active' ORDER BY first_name")->fetchAll(\PDO::FETCH_ASSOC); }
}
