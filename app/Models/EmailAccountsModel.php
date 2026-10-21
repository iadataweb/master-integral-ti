<?php
class EmailAccountsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_email_accounts(): array
    {
        $sql = "SELECT id_email_account, name_email_account, email_email_account, phone_email_account,
                       status_email_account, date_updated_email_account
                FROM email_accounts
                WHERE status_deleted_email_account = 0
                ORDER BY id_email_account DESC";
        return $this->select_all($sql);
    }

    public function show_email_account(int $id): array|false
    {
        $sql = "SELECT * FROM email_accounts WHERE id_email_account = ? AND status_deleted_email_account = 0";
        $row = $this->select($sql, [$id]);
        if ($row) {
            $row['password_plain_email_account'] = $this->decrypt_password($row['password_email_account']);
        }
        return $row;
    }

    public function email_exists(string $email, int $ignore_id = 0): bool
    {
        $sql = "SELECT id_email_account FROM email_accounts WHERE email_email_account = ? AND status_deleted_email_account = 0 AND id_email_account <> ? LIMIT 1";
        return (bool)$this->select($sql, [$email, $ignore_id]);
    }

    public function insert_email_account(array $data): int
    {
        $sql = "INSERT INTO email_accounts(
                    name_email_account,
                    email_email_account,
                    phone_email_account,
                    password_email_account,
                    description_email_account,
                    status_email_account,
                    id_user_created_email_account
                ) VALUES (?,?,?,?,?,?,?)";
        $id = (int)$this->insert($sql, [
            $data['name'],
            $data['email'],
            $data['phone'],
            $this->encrypt_password($data['password']),
            $data['description'],
            $data['status'],
            $data['id_user']
        ]);
        if ($id > 0) {
            $this->insert_password_history($id, $data['password'], $data['id_user'], 'Registro inicial.');
        }
        return $id;
    }

    public function update_email_account(int $id, array $data): bool
    {
        $current = $this->show_email_account($id);
        if (!$current) {
            return false;
        }

        $password_changed = $data['password'] !== '';
        if ($password_changed) {
            $sql = "UPDATE email_accounts SET
                        name_email_account = ?,
                        email_email_account = ?,
                        phone_email_account = ?,
                        password_email_account = ?,
                        description_email_account = ?,
                        status_email_account = ?,
                        id_user_updated_email_account = ?
                    WHERE id_email_account = ?";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'],
                $this->encrypt_password($data['password']),
                $data['description'],
                $data['status'],
                $data['id_user'],
                $id
            ];
        } else {
            $sql = "UPDATE email_accounts SET
                        name_email_account = ?,
                        email_email_account = ?,
                        phone_email_account = ?,
                        description_email_account = ?,
                        status_email_account = ?,
                        id_user_updated_email_account = ?
                    WHERE id_email_account = ?";
            $params = [
                $data['name'],
                $data['email'],
                $data['phone'],
                $data['description'],
                $data['status'],
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

    public function delete_email_account(int $id, int $id_user): bool
    {
        $deleted = $this->update("UPDATE email_accounts SET status_deleted_email_account = 1, id_user_updated_email_account = ? WHERE id_email_account = ?", [$id_user, $id]);
        if ($deleted) {
        }
        return $deleted;
    }

    public function show_password_history(int $id): array
    {
        $sql = "SELECT h.*, TRIM(CONCAT(
                    COALESCE(pt.abbreviation_professional_title, ''), ' ',
                    COALESCE(
                        CASE e.preferred_name_employee
                            WHEN 1 THEN e.first_name_employee
                            WHEN 2 THEN e.middle_name_employee
                            ELSE e.first_name_employee
                        END,
                        e.first_name_employee,
                        u.name_user,
                        'Sistema'
                    ), ' ',
                    COALESCE(e.paternal_last_name_employee, ''), ' ',
                    COALESCE(e.maternal_last_name_employee, '')
                )) AS display_name
                FROM email_account_password_history h
                LEFT JOIN users u ON u.id_user = h.id_user_email_account_password_history
                LEFT JOIN employees e ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_email_account = ?
                ORDER BY h.date_created_email_account_password_history DESC";
        return $this->select_all($sql, [$id]);
    }



    public function available_asset_email_accounts(int $id_asset): array
    {
        $sql = "SELECT
                    ea.id_email_account,
                    ea.name_email_account,
                    ea.email_email_account,
                    ea.phone_email_account,
                    ea.status_email_account
                FROM email_accounts ea
                WHERE ea.status_deleted_email_account = 0
                AND ea.status_email_account = 'active'
                AND NOT EXISTS (
                    SELECT 1
                    FROM asset_email_account_assignments aeaa
                    WHERE aeaa.id_email_account = ea.id_email_account
                    AND aeaa.id_asset = ?
                    AND aeaa.status_asset_email_account_assignment = 'active'
                )
                ORDER BY ea.name_email_account ASC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function asset_current_email_accounts(int $id_asset): array
    {
        $sql = "SELECT
                    aeaa.id_asset_email_account_assignment,
                    aeaa.observation_asset_email_account_assignment,
                    aeaa.date_assigned_asset_email_account_assignment,
                    ea.id_email_account,
                    ea.name_email_account,
                    ea.email_email_account,
                    ea.phone_email_account
                FROM asset_email_account_assignments aeaa
                INNER JOIN email_accounts ea
                    ON ea.id_email_account = aeaa.id_email_account
                WHERE aeaa.id_asset = ?
                AND aeaa.status_asset_email_account_assignment = 'active'
                ORDER BY aeaa.date_assigned_asset_email_account_assignment DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function asset_email_account_history(int $id_asset): array
    {
        $sql = "SELECT
                    h.*,
                    ea.name_email_account,
                    ea.email_email_account,
                    CONCAT(
                        COALESCE(pt.abbreviation_professional_title, ''), ' ',
                        COALESCE(e.first_name_employee, ''), ' ',
                        COALESCE(e.paternal_last_name_employee, ''), ' ',
                        COALESCE(e.maternal_last_name_employee, '')
                    ) AS display_name
                FROM asset_email_account_assignment_history h
                INNER JOIN email_accounts ea
                    ON ea.id_email_account = h.id_email_account
                LEFT JOIN users u
                    ON u.id_user = h.id_user
                LEFT JOIN employees e
                    ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt
                    ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_asset = ?
                ORDER BY h.date_created_asset_email_account_assignment_history DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function active_asset_email_account_exists(int $id_asset, int $id_email_account): bool
    {
        $sql = "SELECT COUNT(*) AS total
                FROM asset_email_account_assignments
                WHERE id_asset = ?
                AND id_email_account = ?
                AND status_asset_email_account_assignment = 'active'";
        $row = $this->select($sql, [$id_asset, $id_email_account]);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function assign_asset_email_account(int $id_asset, int $id_email_account, string $observation, int $id_user): int
    {
        $email = $this->show_email_account($id_email_account);
        if (!$email || $email['status_email_account'] !== 'active' || $this->active_asset_email_account_exists($id_asset, $id_email_account)) {
            return 0;
        }
        $sql = "INSERT INTO asset_email_account_assignments(
                    id_asset,
                    id_email_account,
                    status_asset_email_account_assignment,
                    observation_asset_email_account_assignment,
                    id_user_assigned
                ) VALUES (?, ?, 'active', ?, ?)";
        $id_assignment = (int)$this->insert($sql, [$id_asset, $id_email_account, $observation, $id_user]);
        if ($id_assignment > 0) {
            $this->insert_asset_email_account_history($id_asset, $id_email_account, 'assigned', $observation, $id_user);
        }
        return $id_assignment;
    }

    public function release_asset_email_account(int $id_assignment, int $id_user, string $observation = ''): bool
    {
        $assignment = $this->select("SELECT * FROM asset_email_account_assignments WHERE id_asset_email_account_assignment = ? AND status_asset_email_account_assignment = 'active'", [$id_assignment]);
        if (!$assignment) {
            return false;
        }
        $updated = $this->update(
            "UPDATE asset_email_account_assignments
             SET status_asset_email_account_assignment = 'released',
                 date_released_asset_email_account_assignment = CURRENT_TIMESTAMP,
                 id_user_released = ?
             WHERE id_asset_email_account_assignment = ?",
            [$id_user, $id_assignment]
        );
        if ($updated) {
            $this->insert_asset_email_account_history(
                (int)$assignment['id_asset'],
                (int)$assignment['id_email_account'],
                'released',
                $observation,
                $id_user
            );
        }
        return $updated;
    }

    private function insert_asset_email_account_history(int $id_asset, int $id_email_account, string $action, string $observation, int $id_user): int
    {
        $sql = "INSERT INTO asset_email_account_assignment_history(
                    id_asset,
                    id_email_account,
                    action_asset_email_account_assignment_history,
                    observation_asset_email_account_assignment_history,
                    id_user
                ) VALUES (?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id_asset, $id_email_account, $action, $observation, $id_user]);
    }
    private function insert_password_history(int $id, string $password, int $id_user, string $observation): int
    {
        $sql = "INSERT INTO email_account_password_history(
                    id_email_account,
                    password_fingerprint_email_account_password_history,
                    id_user_email_account_password_history,
                    observation_email_account_password_history
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
