<?php
class Tasks extends Controllers
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        if (empty($_SESSION['login'])) {
            header('Location: '.base_url().'/login');
            exit;
        }
        check_user_status($_SESSION['user_data']['id_user']);
        if (empty($_SESSION['status_active_user'])) {
            header('Location: '.base_url().'/account/logout');
            exit;
        }
        parent::__construct();
        check_permissions(MODULE_TASKS);
    }

    private function allowed(string $permission): void
    {
        if (empty($_SESSION['module_permissions'][$permission])) {
            http_response_code(403);
            if ($permission === 'r_read_permission') {
                header('Location: '.base_url().'/errors/forbidden');
                exit;
            }
            $this->respond(false, 'No tienes permisos para realizar esta acción.');
        }
    }

    private function respond(bool $status, string $message): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status'=>$status, 'type'=>$status ? 'success' : 'error', 'message'=>$message], JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function tasks()
    {
        $this->index();
    }

    public function index()
    {
        $this->allowed('r_read_permission');
        $data['css'] = ['/extensions/datatables/datatables.min.css'];
        $data['script'] = ['/js/language_datatable.js', '/extensions/datatables/datatables.min.js'];
        $data['page_functions_js'] = ['functions_tasks.js'];
        $this->views->getView($this, '', 'index', $data);
    }

    public function listing()
    {
        $this->allowed('r_read_permission');
        $tasks = $this->model->all();
        foreach ($tasks as &$task) {
            $task['overdue'] = !empty($task['due_date']) && $task['due_date'] < date('Y-m-d') && !in_array($task['status'], ['Completada','Cancelada'], true);
            $id = (int)$task['id_task'];
            $task['options'] = '<div class="d-flex gap-1">';
            foreach (['open'=>['Ver','eye','secondary','r_read_permission'], 'edit'=>['Editar','pencil','primary','u_update_permission'], 'delete'=>['Eliminar','trash','danger','d_delete_permission']] as $action=>$button) {
                if (empty($_SESSION['module_permissions'][$button[3]])) continue;
                $task['options'] .= '<button type="button" class="btn icon btn-'.$button[2].' js-task-'.$action.'" data-id="'.$id.'" title="'.$button[0].'" aria-label="'.$button[0].'"><i class="bx bx-'.$button[1].'" aria-hidden="true"></i></button>';
            }
            $task['options'] .= '</div>';
        }
        unset($task);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($tasks, JSON_UNESCAPED_UNICODE);
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $id = (int)($_POST['id_task'] ?? 0);
        $this->allowed($id > 0 ? 'u_update_permission' : 'w_write_permission');
        if ($id < 0 || ($id > 0 && !$this->model->find($id))) $this->respond(false, 'La tarea no existe.');
        $task = [];
        foreach (['title'=>180,'requester'=>150,'priority'=>12,'status'=>15,'due_date'=>10,'description'=>10000,'result'=>10000] as $key=>$limit) {
            if (isset($_POST[$key]) && !is_string($_POST[$key])) $this->respond(false, 'Datos inválidos.');
            $task[$key] = trim($_POST[$key] ?? '');
            if (mb_strlen($task[$key]) > $limit) $this->respond(false, 'El campo supera la longitud permitida.');
        }
        if ($task['title'] === '' || $task['requester'] === '') $this->respond(false, 'Completa la tarea y el solicitante.');
        if (!in_array($task['priority'], ['Baja','Media','Alta'], true) || !in_array($task['status'], ['Pendiente','En progreso','Completada','Cancelada'], true)) $this->respond(false, 'Selecciona una prioridad y un estado válidos.');
        if ($task['due_date'] !== '') {
            $date = DateTime::createFromFormat('!Y-m-d', $task['due_date']);
            if (!$date || $date->format('Y-m-d') !== $task['due_date']) $this->respond(false, 'La fecha límite no es válida.');
        }
        try {
            $ok = $this->model->save($task, (int)$_SESSION['user_data']['id_user'], $id);
            $this->respond($ok, $ok ? 'Tarea guardada.' : 'No se pudo guardar la tarea.');
        } catch (Throwable $e) {
            error_log('Tasks save: '.$e->getMessage());
            $this->respond(false, 'No se pudo guardar la tarea.');
        }
    }

    public function remove()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
        $this->allowed('d_delete_permission');
        $id = (int)($_POST['id_task'] ?? 0);
        if (!$this->model->find($id)) $this->respond(false, 'La tarea no existe.');
        $ok = $this->model->remove($id);
        $this->respond($ok, $ok ? 'Tarea eliminada.' : 'No se pudo eliminar la tarea.');
    }
}
