<?php
declare(strict_types=1);
namespace Clinic\Models;
use PDO; use Clinic\Config\Database;

abstract class BaseModel {
    protected PDO $db;
    protected string $table;
    protected string $primaryKey = 'id';
    public function __construct() { $this->db = Database::getConnection(); }
    public function find(int $id): ?array { $stmt=$this->db->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey}=:id"); $stmt->execute([':id'=>$id]); return $stmt->fetch(PDO::FETCH_ASSOC)?:null; }
    public function all(array $cond=[], string $order=''): array {
        $sql="SELECT * FROM {$this->table}"; $p=[];
        if(!empty($cond)){$w=[];foreach($cond as $k=>$v){$w[]="{$k}=:{$k}";$p[":{$k}"]=$v;} $sql.=" WHERE ".implode(' AND ',$w);}
        if($order)$sql.=" ORDER BY $order"; $stmt=$this->db->prepare($sql); $stmt->execute($p); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function create(array $d): int|false {
        $cols=implode(', ',array_keys($d)); $ph=':'.implode(', :',array_keys($d));
        $stmt=$this->db->prepare("INSERT INTO {$this->table} ($cols) VALUES ($ph)");
        return $stmt->execute($d)?(int)$this->db->lastInsertId():false;
    }
    public function update(int $id, array $d): bool {
        $u=[];foreach(array_keys($d) as $k)$u[]="{$k}=:{$k}";
        $d['pk_id']=$id; $stmt=$this->db->prepare("UPDATE {$this->table} SET ".implode(', ',$u)." WHERE {$this->primaryKey}=:pk_id");
        return $stmt->execute($d);
    }
    public function delete(int $id): bool { return $this->db->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey}=:id")->execute([':id'=>$id]); }
    public function count(array $cond=[]): int {
        $sql="SELECT COUNT(*) as c FROM {$this->table}"; $p=[];
        if(!empty($cond)){$w=[];foreach($cond as $k=>$v){$w[]="{$k}=:{$k}";$p[":{$k}"]=$v;} $sql.=" WHERE ".implode(' AND ',$w);}
        $stmt=$this->db->prepare($sql); $stmt->execute($p); return (int)$stmt->fetch(PDO::FETCH_ASSOC)['c'];
    }
    public function getDb(): PDO { return $this->db; }
}
