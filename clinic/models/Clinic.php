<?php
declare(strict_types=1);
namespace Clinic\Models;
use PDO;

class Patient extends BaseModel {
    protected string $table = 'patients';
    protected string $primaryKey = 'patient_id';
    public function search(array $f=[]): array {
        $sql="SELECT * FROM patients WHERE 1=1"; $p=[];
        if(!empty($f['status'])){$sql.=" AND status=:st";$p[':st']=$f['status'];}
        if(!empty($f['search'])){$sql.=" AND (student_number LIKE :s1 OR first_name LIKE :s2 OR last_name LIKE :s3 OR CONCAT(first_name,' ',last_name) LIKE :s4)";
            $t='%'.$f['search'].'%'; $p[':s1']=$p[':s2']=$p[':s3']=$p[':s4']=$t;}
        $sql.=" ORDER BY created_at DESC";
        $stmt=$this->db->prepare($sql); $stmt->execute($p); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class MedicalRecord extends BaseModel {
    protected string $table = 'medical_records';
    protected string $primaryKey = 'record_id';
    public function getByPatient(int $pid): array {
        $sql="SELECT mr.*, CONCAT(u.first_name,' ',u.last_name) as recorded_by_name FROM medical_records mr LEFT JOIN users u ON mr.recorded_by=u.user_id WHERE mr.patient_id=:pid ORDER BY mr.record_date DESC";
        $stmt=$this->db->prepare($sql); $stmt->execute([':pid'=>$pid]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class Consultation extends BaseModel {
    protected string $table = 'consultations';
    protected string $primaryKey = 'consultation_id';
    public function getAllWithPatient(array $f=[]): array {
        $sql="SELECT c.*, CONCAT(p.first_name,' ',p.last_name) as patient_name, p.student_number, CONCAT(d.first_name,' ',d.last_name) as doctor_name FROM consultations c LEFT JOIN patients p ON c.patient_id=p.patient_id LEFT JOIN users d ON c.attending_doctor=d.user_id WHERE 1=1";
        $p=[];
        if(!empty($f['status'])){$sql.=" AND c.status=:st";$p[':st']=$f['status'];}
        if(!empty($f['search'])){$sql.=" AND (p.student_number LIKE :s1 OR CONCAT(p.first_name,' ',p.last_name) LIKE :s2)";$t='%'.$f['search'].'%';$p[':s1']=$p[':s2']=$t;}
        $sql.=" ORDER BY c.consultation_date DESC";
        $stmt=$this->db->prepare($sql); $stmt->execute($p); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getWithDetails(int $id): ?array {
        $sql="SELECT c.*, CONCAT(p.first_name,' ',p.last_name) as patient_name, p.student_number, p.blood_type, p.allergies, p.patient_id, CONCAT(d.first_name,' ',d.last_name) as doctor_name, CONCAT(n.first_name,' ',n.last_name) as nurse_name FROM consultations c LEFT JOIN patients p ON c.patient_id=p.patient_id LEFT JOIN users d ON c.attending_doctor=d.user_id LEFT JOIN users n ON c.nurse_on_duty=n.user_id WHERE c.consultation_id=:id";
        $stmt=$this->db->prepare($sql); $stmt->execute([':id'=>$id]); return $stmt->fetch(PDO::FETCH_ASSOC)?:null;
    }
}

class Medicine extends BaseModel {
    protected string $table = 'medicines';
    protected string $primaryKey = 'medicine_id';
    public function getAvailable(): array { return $this->db->query("SELECT * FROM medicines WHERE stock_quantity>0 AND status!='Expired' ORDER BY name")->fetchAll(PDO::FETCH_ASSOC); }
    public function updateStock(int $id, int $qty): bool { return $this->db->prepare("UPDATE medicines SET stock_quantity=stock_quantity-? WHERE medicine_id=? AND stock_quantity>=?")->execute([$qty,$id,$qty]); }
    public function refreshStatuses(): void {
        $this->db->exec("UPDATE medicines SET status='Expired' WHERE expiry_date < CURDATE() AND status!='Expired'");
        $this->db->exec("UPDATE medicines SET status='Out of Stock' WHERE stock_quantity=0 AND status NOT IN ('Expired')");
        $this->db->exec("UPDATE medicines SET status='Low Stock' WHERE stock_quantity>0 AND stock_quantity<=reorder_level AND status NOT IN ('Expired')");
        $this->db->exec("UPDATE medicines SET status='Available' WHERE stock_quantity>reorder_level AND status NOT IN ('Expired')");
    }
}

class MedicineDispensing extends BaseModel {
    protected string $table = 'medicine_dispensing';
    protected string $primaryKey = 'dispensing_id';
    public function getAllWithDetails(): array {
        $sql="SELECT md.*, m.name as medicine_name, CONCAT(p.first_name,' ',p.last_name) as patient_name, p.student_number, CONCAT(u.first_name,' ',u.last_name) as dispensed_by_name FROM medicine_dispensing md LEFT JOIN medicines m ON md.medicine_id=m.medicine_id LEFT JOIN patients p ON md.patient_id=p.patient_id LEFT JOIN users u ON md.dispensed_by=u.user_id ORDER BY md.dispensed_date DESC LIMIT 200";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}

class MedicalClearance extends BaseModel {
    protected string $table = 'medical_clearances';
    protected string $primaryKey = 'clearance_id';
    public function getAllWithPatient(array $f=[]): array {
        $sql="SELECT mc.*, CONCAT(p.first_name,' ',p.last_name) as patient_name, p.student_number, CONCAT(u.first_name,' ',u.last_name) as issued_by_name FROM medical_clearances mc LEFT JOIN patients p ON mc.patient_id=p.patient_id LEFT JOIN users u ON mc.issued_by=u.user_id WHERE 1=1";
        $pa=[];
        if(!empty($f['status'])){$sql.=" AND mc.status=:st";$pa[':st']=$f['status'];}
        $sql.=" ORDER BY mc.created_at DESC";
        $stmt=$this->db->prepare($sql);$stmt->execute($pa);return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class HealthIncident extends BaseModel {
    protected string $table = 'health_incidents';
    protected string $primaryKey = 'incident_id';
    public function getAllWithDetails(array $f=[]): array {
        $sql="SELECT hi.*, CONCAT(p.first_name,' ',p.last_name) as patient_name, p.student_number, CONCAT(u.first_name,' ',u.last_name) as reported_by_name FROM health_incidents hi LEFT JOIN patients p ON hi.patient_id=p.patient_id LEFT JOIN users u ON hi.reported_by=u.user_id WHERE 1=1";
        $pa=[];
        if(!empty($f['status'])){$sql.=" AND hi.status=:st";$pa[':st']=$f['status'];}
        if(!empty($f['severity'])){$sql.=" AND hi.severity=:sv";$pa[':sv']=$f['severity'];}
        $sql.=" ORDER BY hi.incident_date DESC";
        $stmt=$this->db->prepare($sql);$stmt->execute($pa);return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

class AuditLog extends BaseModel {
    protected string $table = 'audit_logs';
    protected string $primaryKey = 'log_id';
    public function getAll(array $f=[], int $limit=100): array {
        $sql="SELECT al.*, CONCAT(u.first_name,' ',u.last_name) as user_name FROM audit_logs al LEFT JOIN users u ON al.user_id=u.user_id WHERE 1=1";
        $p=[];
        if(!empty($f['module'])){$sql.=" AND al.module=:m";$p[':m']=$f['module'];}
        if(!empty($f['search'])){$sql.=" AND al.action LIKE :s";$p[':s']='%'.$f['search'].'%';}
        $sql.=" ORDER BY al.created_at DESC LIMIT $limit";
        $stmt=$this->db->prepare($sql);$stmt->execute($p);return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getModules(): array { return array_column($this->db->query("SELECT DISTINCT module FROM audit_logs ORDER BY module")->fetchAll(PDO::FETCH_ASSOC),'module'); }
}
