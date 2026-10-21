<?php
class OsUserAccountsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_os_user_accounts(): array
    {
        $sql = "SELECT id_os_user_account, name_os_user_account, username_os_user_account,
                       password_updated_at_os_user_account, status_active_os_user_account,
                       date_updated_os_user_account
                FROM os_user_accounts
                WHERE status_deleted_os_user_account = 0
                ORDER BY id_os_user_account DESC";
        return $this->select_all($sql);
    }

    public function show_os_user_account(int $id): array|false
    {
        $sql = "SELECT * FROM os_user_accounts WHERE id_os_user_account = ? AND status_deleted_os_user_account = 0";
        $row = $this->select($sql, [$id]);
        if ($row) {
            $row['password_plain_os_user_account'] = $this->decrypt_password($row['password_os_user_account']);
        }
        return $row;
    }

    public function insert_os_user_account(array $data): int
    {
        $sql = "INSERT INTO os_user_accounts(
                    name_os_user_account,
                    username_os_user_account,
                    password_os_user_account,
                    description_os_user_account,
                    password_updated_at_os_user_account,
                    status_active_os_user_account,
                    id_user_created_os_user_account
                ) VALUES (?,?,?,?,?,?,?)";
        $id = (int)$this->insert($sql, [
            $data['name'],
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

    public function update_os_user_account(int $id, array $data): bool
    {
        $current = $this->show_os_user_account($id);
        if (!$current) {
            return false;
        }
        $password_changed = $data['password'] !== '';
        if ($password_changed) {
            $sql = "UPDATE os_user_accounts SET
                        name_os_user_account = ?,
                        username_os_user_account = ?,
                        password_os_user_account = ?,
                        description_os_user_account = ?,
                        password_updated_at_os_user_account = ?,
                        status_active_os_user_account = ?,
                        id_user_updated_os_user_account = ?
                    WHERE id_os_user_account = ?";
            $params = [
                $data['name'],
                $data['username'],
                $this->encrypt_password($data['password']),
                $data['description'],
                $data['password_updated_at'],
                $data['status_active'],
                $data['id_user'],
                $id
            ];
        } else {
            $sql = "UPDATE os_user_accounts SET
                        name_os_user_account = ?,
                        username_os_user_account = ?,
                        description_os_user_account = ?,
                        password_updated_at_os_user_account = ?,
                        status_active_os_user_account = ?,
                        id_user_updated_os_user_account = ?
                    WHERE id_os_user_account = ?";
            $params = [
                $data['name'],
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
                $this->insert_password_history($id, $data['password'], $data['id_user'], 'Cambio de contrasena.');
            }
        }
        return $updated;
    }

    public function delete_os_user_account(int $id, int $id_user): bool
    {
        $deleted = $this->update("UPDATE os_user_accounts SET status_deleted_os_user_account = 1, id_user_updated_os_user_account = ? WHERE id_os_user_account = ?", [$id_user, $id]);
        if ($deleted) {
        }
        return $deleted;
    }

    public function show_password_history(int $id): array
    {
        $sql = "SELECT h.*, CONCAT(COALESCE(pt.abbreviation_professional_title, ''), ' ', COALESCE(e.first_name_employee, ''), ' ', COALESCE(e.paternal_last_name_employee, '')) AS display_name
                FROM os_user_account_password_history h
                LEFT JOIN users u ON u.id_user = h.id_user_os_user_account_password_history
                LEFT JOIN employees e ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_os_user_account = ?
                ORDER BY h.date_created_os_user_account_password_history DESC";
        return $this->select_all($sql, [$id]);
    }



    private function insert_password_history(int $id, string $password, int $id_user, string $observation): int
    {
        $sql = "INSERT INTO os_user_account_password_history(
                    id_os_user_account,
                    password_fingerprint_os_user_account_password_history,
                    id_user_os_user_account_password_history,
                    observation_os_user_account_password_history
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