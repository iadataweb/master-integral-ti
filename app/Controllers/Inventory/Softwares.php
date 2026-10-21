<?php
class Softwares extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['login'])) { header('Location: '.base_url().'/login'); exit; }
        check_user_status($_SESSION['user_data']['id_user']);
    }
    private function allowed(string $permission): bool
    {
        check_permissions(MODULE_INVENTORY);
        if ($_SESSION['status_active_user'] === 0 || empty($_SESSION['module_permissions'][$permission])) {
            $this->respond(false,'No tienes permisos para realizar esta acción.');
            return false;
        }
        return true;
    }
    private function respond(bool $status, string $message): void
    {
        echo json_encode(['status'=>$status,'type'=>$status?'success':'error','message'=>$message],JSON_UNESCAPED_UNICODE);
    }
    private function inputId(string $name): int
    {
        return max(0, (int)filter_var($_POST[$name] ?? null,FILTER_VALIDATE_INT));
    }
    public function show_softwares_by_asset()
    {
        if (!$this->allowed('r_read_permission')) return;
        $id=$this->inputId('id_asset_software');
        $asset=$this->model->asset($id);
        if (!$asset) { $this->respond(false,'No se encontró el activo.'); return; }
        echo json_encode(['status'=>true,'asset'=>$asset,'result'=>$this->model->show_softwares_by_asset($id),'catalogs'=>$this->model->catalogs(),'permissions'=>[
            'create'=>!empty($_SESSION['module_permissions']['w_write_permission']),
            'edit'=>!empty($_SESSION['module_permissions']['u_update_permission']),
            'delete'=>!empty($_SESSION['module_permissions']['d_delete_permission'])
        ]], JSON_UNESCAPED_UNICODE);
    }
    public function save_software()
    {
        $id=$this->inputId('id_software');
        if (!$this->allowed($id>0?'u_update_permission':'w_write_permission')) return;
        $asset=$this->inputId('id_asset_software');
        $existing=$id>0?$this->model->software($id,$asset):false;
        if (!$this->model->asset($asset) || ($id>0 && !$existing)) { $this->respond(false,'El software no corresponde al activo seleccionado.'); return; }
        $data=[];
        foreach (['name_software'=>200,'version_software'=>100,'description_software'=>5000] as $field=>$limit) {
            $value=$_POST[$field]??'';
            if (!is_string($value) || mb_strlen(trim($value))>$limit) { $this->respond(false,'Verifica la longitud de los campos del programa.'); return; }
            $data[$field]=trim($value);
        }
        if ($data['name_software']==='') { $this->respond(false,'Escribe el nombre del programa.'); return; }
        $catalogs=$this->model->catalogs();
        foreach (['id_type_license_software'=>'licenses','id_type_software_software'=>'types','id_architecture_software'=>'architectures'] as $field=>$catalog) {
            $value=$this->inputId($field);
            if (!$value || (!in_array($value,array_map('intval',array_column($catalogs[$catalog],'id')),true) && (!$existing || (int)$existing[$field]!==$value))) { $this->respond(false,'Selecciona una licencia, tipo y arquitectura válidos.'); return; }
            $data[$field]=$value;
        }
        $saved=$this->model->save_software($asset,$id,$data);
        $this->respond($saved,$saved?($id>0?'Software actualizado correctamente.':'Software registrado correctamente.'):'No se pudo guardar el programa.');
    }
    public function delete_software()
    {
        if (!$this->allowed('d_delete_permission')) return;
        $id=$this->inputId('id_software'); $asset=$this->inputId('id_asset_software');
        if (!$this->model->asset($asset) || !$this->model->software($id,$asset)) { $this->respond(false,'El software no corresponde al activo seleccionado.'); return; }
        $deleted=$this->model->delete_software($id,$asset);
        $this->respond($deleted,$deleted?'Software eliminado correctamente.':'No se pudo eliminar el programa.');
    }
}
