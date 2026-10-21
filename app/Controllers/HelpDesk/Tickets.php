<?php
require_once("Controllers/HelpDesk/Incidents.php");

class Tickets extends Incidents
{
    private function redirect_to_incidents(string $method, $id = null): void
    {
        $url = base_url() . "/help_desk/incidents/" . $method;
        if ($id !== null && $id !== '') {
            $url .= "/" . intval($id);
        }
        header("Location: " . $url);
        die();
    }

    public function manage_tickets() { $this->redirect_to_incidents('manage_incidents'); }
    public function new_ticket() { $this->redirect_to_incidents('new_incident'); }
    public function list_of_tickets() { $this->redirect_to_incidents('list_of_incidents'); }
    public function ticket_details($id_ticket) { $this->redirect_to_incidents('incident_details', $id_ticket); }

    public function show_tickets() { return $this->show_incidents(); }
    public function show_ticket() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->show_incident(); }
    public function insert_ticket() { return $this->insert_incident(); }
    public function update_ticket() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->update_incident(); }
    public function delete_ticket() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->delete_incident(); }
    public function close_ticket() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->close_incident(); }
    public function reopen_ticket() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->reopen_incident(); }
    public function relate_ticket_asset() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); return $this->relate_incident_asset(); }
    public function save_ticket_diagnostic() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); $_POST['diagnostic_incident'] = $_POST['diagnostic_ticket'] ?? ($_POST['diagnostic_incident'] ?? null); return $this->save_incident_diagnostic(); }
    public function save_ticket_solution() { $_POST['id_incident'] = $_POST['id_ticket'] ?? ($_POST['id_incident'] ?? null); $_POST['solution_incident'] = $_POST['solution_ticket'] ?? ($_POST['solution_incident'] ?? null); return $this->save_incident_solution(); }
}
