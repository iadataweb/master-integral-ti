<?php
class WindowsCredentialsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_windows_credentials(): array
    {
        $sql = "SELECT id_windows_credential, name_windows_credential, network_address_windows_credential,
                       username_windows_credential, password_updated_at_windows_credential,
                       status_active_windows_credential, date_updated_windows_credential
                FROM windows_credentials
                WHERE status_deleted_windows_credential = 0
                ORDER BY id_windows_credential DESC";
        return $this->select_all($sql);
    }

    public function show_windows_credential(int $id): array|false
    {
        $sql = "SELECT * FROM windows_credentials WHERE id_windows_credential = ? AND status_deleted_windows_credential = 0";
        $row = $this->select($sql, [$id]);
        if ($row) {
            $row['password_plain_windows_credential'] = $this->decrypt_password($row['password_windows_credential']);
        }
        return $row;
    }

    public function insert_windows_credential(array $data): int
    {
        $sql = "INSERT INTO windows_credentials(
                    name_windows_credential,
                    network_address_windows_credential,
                    username_windows_credential,
                    password_windows_credential,
                    description_windows_credential,
                    password_updated_at_windows_credential,
                    status_active_windows_credential,
                    id_user_created_windows_credential
                ) VALUES (?,?,?,?,?,?,?,?)";
        $id = (int)$this->insert($sql, [
            $data['name'],
            $data['network_address'],
            $data['username'],
            $this->encrypt_password($data['password']),
            $data['description'],
            $data['password_updated_at'],
            $data['status_active'],
            $data['id_user']
        ]);
        if ($id > 0) {
            $this->insert_password_history($id, $data['password'], $data['id_user'], 'Registro inicial.');
        }
        return $id;
    }

    public function update_windows_credential(int $id, array $data): bool
    {
        $current = $this->show_windows_credential($id);
        if (!$current) {
            return false;
        }

        $password_changed = $data['password'] !== '';
        if ($password_changed) {
            $sql = "UPDATE windows_credentials SET
                        name_windows_credential = ?,
                        network_address_windows_credential = ?,
                        username_windows_credential = ?,
                        password_windows_credential = ?,
                        description_windows_credential = ?,
                        password_updated_at_windows_credential = ?,
                        status_active_windows_credential = ?,
                        id_user_updated_windows_credential = ?
                    WHERE id_windows_credential = ?";
            $params = [
                $data['name'],
                $data['network_address'],
                $data['username'],
                $this->encrypt_password($data['password']),
                $data['description'],
                $data['password_updated_at'],
                $data['status_active'],
                $data['id_user'],
                $id
            ];
        } else {
            $sql = "UPDATE windows_credentials SET
                        name_windows_credential = ?,
                        network_address_windows_credential = ?,
                        username_windows_credential = ?,
                        description_windows_credential = ?,
                        password_updated_at_windows_credential = ?,
                        status_active_windows_credential = ?,
                        id_user_updated_windows_credential = ?
                    WHERE id_windows_credential = ?";
            $params = [
                $data['name'],
                $data['network_address'],
                $data['username'],
                $data['description'],
                $data['password_updated_at'],
                $data['status_active'],
                $data['id_user'],
                $id
            ];
        }

        $updated = $this->update($sql, $params);
        if ($updated) {
            if ($password_changed) {
                $this->insert_password_history($id, $data['password'], $data['id_user'], 'Cambio de password.');
            }
        }
        return $updated;
    }

    public function delete_windows_credential(int $id, int $id_user): bool
    {
        $deleted = $this->update("UPDATE windows_credentials SET status_deleted_windows_credential = 1, id_user_updated_windows_credential = ? WHERE id_windows_credential = ?", [$id_user, $id]);
        if ($deleted) {
        }
        return $deleted;
    }

    public function show_password_history(int $id): array
    {
        $sql = "SELECT h.*, CONCAT(COALESCE(pt.abbreviation_professional_title, ''), ' ', COALESCE(e.first_name_employee, ''), ' ', COALESCE(e.paternal_last_name_employee, '')) AS display_name
                FROM windows_credential_password_history h
                LEFT JOIN users u ON u.id_user = h.id_user_windows_credential_password_history
                LEFT JOIN employees e ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_windows_credential = ?
                ORDER BY h.date_created_windows_credential_password_history DESC";
        return $this->select_all($sql, [$id]);
    }



    public function available_asset_windows_credentials(int $id_asset): array
    {
        $sql = "SELECT
                    wc.id_windows_credential,
                    wc.name_windows_credential,
                    wc.network_address_windows_credential,
                    wc.username_windows_credential
                FROM windows_credentials wc
                WHERE wc.status_deleted_windows_credential = 0
                AND wc.status_active_windows_credential = 1
                AND NOT EXISTS (
                    SELECT 1
                    FROM asset_windows_credential_assignments awca
                    WHERE awca.id_windows_credential_asset_windows_credential_assignment = wc.id_windows_credential
                    AND awca.id_asset_asset_windows_credential_assignment = ?
                    AND awca.status_asset_windows_credential_assignment = 'active'
                )
                ORDER BY wc.name_windows_credential ASC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function asset_current_windows_credentials(int $id_asset): array
    {
        $sql = "SELECT
                    awca.id_asset_windows_credential_assignment,
                    awca.observation_asset_windows_credential_assignment,
                    awca.date_assigned_asset_windows_credential_assignment,
                    wc.id_windows_credential,
                    wc.name_windows_credential,
                    wc.network_address_windows_credential,
                    wc.username_windows_credential
                FROM asset_windows_credential_assignments awca
                INNER JOIN windows_credentials wc
                    ON wc.id_windows_credential = awca.id_windows_credential_asset_windows_credential_assignment
                WHERE awca.id_asset_asset_windows_credential_assignment = ?
                AND awca.status_asset_windows_credential_assignment = 'active'
                ORDER BY awca.date_assigned_asset_windows_credential_assignment DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function asset_windows_credential_history(int $id_asset): array
    {
        $sql = "SELECT
                    h.*,
                    wc.name_windows_credential,
                    wc.network_address_windows_credential,
                    wc.username_windows_credential,
                    CONCAT(
                        COALESCE(pt.abbreviation_professional_title, ''), ' ',
                        COALESCE(e.first_name_employee, ''), ' ',
                        COALESCE(e.paternal_last_name_employee, ''), ' ',
                        COALESCE(e.maternal_last_name_employee, '')
                    ) AS display_name
                FROM asset_windows_credential_assignment_history h
                INNER JOIN windows_credentials wc
                    ON wc.id_windows_credential = h.id_windows_credential
                LEFT JOIN users u
                    ON u.id_user = h.id_user
                LEFT JOIN employees e
                    ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt
                    ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_asset = ?
                ORDER BY h.date_created_asset_windows_credential_assignment_history DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function active_asset_windows_credential_exists(int $id_asset, int $id_windows_credential): bool
    {
        $sql = "SELECT COUNT(*) AS total
                FROM asset_windows_credential_assignments
                WHERE id_asset_asset_windows_credential_assignment = ?
                AND id_windows_credential_asset_windows_credential_assignment = ?
                AND status_asset_windows_credential_assignment = 'active'";
        $row = $this->select($sql, [$id_asset, $id_windows_credential]);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function assign_asset_windows_credential(int $id_asset, int $id_windows_credential, string $observation, int $id_user): int
    {
        $credential = $this->show_windows_credential($id_windows_credential);
        if (!$credential || (int)$credential['status_active_windows_credential'] !== 1 || $this->active_asset_windows_credential_exists($id_asset, $id_windows_credential)) {
            return 0;
        }
        $sql = "INSERT INTO asset_windows_credential_assignments(
                    id_asset_asset_windows_credential_assignment,
                    id_windows_credential_asset_windows_credential_assignment,
                    status_asset_windows_credential_assignment,
                    observation_asset_windows_credential_assignment,
                    id_user_assigned_asset_windows_credential_assignment
                ) VALUES (?, ?, 'active', ?, ?)";
        $id_assignment = (int)$this->insert($sql, [$id_asset, $id_windows_credential, $observation, $id_user]);
        if ($id_assignment > 0) {
            $this->insert_asset_windows_credential_history($id_asset, $id_windows_credential, 'assigned', $observation, $id_user);
        }
        return $id_assignment;
    }

    public function release_asset_windows_credential(int $id_assignment, int $id_user, string $observation = ''): bool
    {
        $assignment = $this->select("SELECT * FROM asset_windows_credential_assignments WHERE id_asset_windows_credential_assignment = ? AND status_asset_windows_credential_assignment = 'active'", [$id_assignment]);
        if (!$assignment) {
            return false;
        }
        $updated = $this->update(
            "UPDATE asset_windows_credential_assignments
             SET status_asset_windows_credential_assignment = 'released',
                 date_released_asset_windows_credential_assignment = CURRENT_TIMESTAMP,
                 id_user_released_asset_windows_credential_assignment = ?
             WHERE id_asset_windows_credential_assignment = ?",
            [$id_user, $id_assignment]
        );
        if ($updated) {
            $this->insert_asset_windows_credential_history(
                (int)$assignment['id_asset_asset_windows_credential_assignment'],
                (int)$assignment['id_windows_credential_asset_windows_credential_assignment'],
                'released',
                $observation,
                $id_user
            );
        }
        return $updated;
    }

    private function insert_asset_windows_credential_history(int $id_asset, int $id_windows_credential, string $action, string $observation, int $id_user): int
    {
        $sql = "INSERT INTO asset_windows_credential_assignment_history(
                    id_asset,
                    id_windows_credential,
                    action_asset_windows_credential_assignment_history,
                    observation_asset_windows_credential_assignment_history,
                    id_user
                ) VALUES (?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id_asset, $id_windows_credential, $action, $observation, $id_user]);
    }
    private function insert_password_history(int $id, string $password, int $id_user, string $observation): int
    {
        $sql = "INSERT INTO windows_credential_password_history(
                    id_windows_credential,
                    password_fingerprint_windows_credential_password_history,
                    id_user_windows_credential_password_history,
                    observation_windows_credential_password_history
                ) VALUES (?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id, hash('sha256', $password), $id_user, $observation]);
    }



    private function encrypt_password(string $password): string
    {
        $iv = random_bytes(16);
        $key = hash('sha256', DB_PASSWORD . '|' . BASE_URL, true);
        $cipher = openssl_encrypt($password, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher);
    }

    private function decrypt_password(string $payload): string
    {
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) <= 16) {
            return '';
        }
        $iv = substr($raw, 0, 16);
        $cipher = substr($raw, 16);
        $key = hash('sha256', DB_PASSWORD . '|' . BASE_URL, true);
        $plain = openssl_decrypt($cipher, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $plain === false ? '' : $plain;
    }
}
