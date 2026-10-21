<?php
class WifiNetworksModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_wifi_networks(): array
    {
        $sql = "SELECT w.id_wifi_network, w.management_ip_wifi_network, w.ssid_wifi_network,
                       w.status_active_wifi_network, w.date_updated_wifi_network,
                       a.code_inventory_asset, ta.name_type_asset, ca.name_category_asset,
                       COALESCE(b.name_brand, 'Ninguna') AS name_brand,
                       COALESCE(co.name_condition_asset, 'No definido') AS name_condition_asset
                FROM wifi_networks w
                INNER JOIN assets a ON a.id_asset = w.id_asset_wifi_network
                LEFT JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
                LEFT JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
                LEFT JOIN brands b ON b.id_brand = a.id_brand_asset
                LEFT JOIN conditions_asset co ON co.id_condition_asset = a.id_condition_asset_asset
                WHERE w.status_deleted_wifi_network = 0
                ORDER BY w.id_wifi_network DESC";
        return $this->select_all($sql);
    }

    public function show_wifi_network(int $id): array|false
    {
        $sql = "SELECT w.*, a.code_inventory_asset, ta.name_type_asset, ca.name_category_asset,
                       COALESCE(b.name_brand, 'Ninguna') AS name_brand,
                       COALESCE(co.name_condition_asset, 'No definido') AS name_condition_asset
                FROM wifi_networks w
                INNER JOIN assets a ON a.id_asset = w.id_asset_wifi_network
                LEFT JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
                LEFT JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
                LEFT JOIN brands b ON b.id_brand = a.id_brand_asset
                LEFT JOIN conditions_asset co ON co.id_condition_asset = a.id_condition_asset_asset
                WHERE w.id_wifi_network = ? AND w.status_deleted_wifi_network = 0";
        $row = $this->select($sql, [$id]);
        if ($row) {
            $row['password_plain_wifi_network'] = $this->decrypt_password($row['password_wifi_network']);
        }
        return $row;
    }

    public function show_assets_for_wifi(): array
    {
        $sql = "SELECT a.id_asset, a.code_inventory_asset, ta.name_type_asset, ca.name_category_asset,
                       COALESCE(b.name_brand, 'Ninguna') AS name_brand,
                       COALESCE(co.name_condition_asset, 'No definido') AS name_condition_asset,
                       a.date_created_asset
                FROM assets a
                LEFT JOIN types_asset ta ON ta.id_type_asset = a.id_type_asset_asset
                LEFT JOIN categories_asset ca ON ca.id_category_asset = a.id_category_asset_asset
                LEFT JOIN brands b ON b.id_brand = a.id_brand_asset
                LEFT JOIN conditions_asset co ON co.id_condition_asset = a.id_condition_asset_asset
                WHERE a.status_deleted_asset = 0
                ORDER BY a.id_asset DESC";
        return $this->select_all($sql);
    }

    public function insert_wifi_network(array $data): int
    {
        $sql = "INSERT INTO wifi_networks(
                    id_asset_wifi_network,
                    management_ip_wifi_network,
                    ssid_wifi_network,
                    password_wifi_network,
                    description_wifi_network,
                    status_active_wifi_network,
                    id_user_created_wifi_network
                ) VALUES (?,?,?,?,?,?,?)";
        $id = (int)$this->insert($sql, [
            $data['id_asset'],
            $data['management_ip'],
            $data['ssid'],
            $this->encrypt_password($data['password']),
            $data['description'],
            $data['status_active'],
            $data['id_user']
        ]);
        if ($id > 0) {
            $this->insert_history($id, null, $data['ssid'], $data['password'], 'created', 'Registro inicial de red WiFi.', $data['id_user']);
        }
        return $id;
    }

    public function update_wifi_network(int $id, array $data): bool
    {
        $current = $this->show_wifi_network($id);
        if (!$current) {
            return false;
        }
        $passwordChanged = $data['password'] !== '';
        if ($passwordChanged) {
            $sql = "UPDATE wifi_networks SET
                        id_asset_wifi_network = ?,
                        management_ip_wifi_network = ?,
                        ssid_wifi_network = ?,
                        password_wifi_network = ?,
                        description_wifi_network = ?,
                        status_active_wifi_network = ?,
                        id_user_updated_wifi_network = ?
                    WHERE id_wifi_network = ?";
            $params = [
                $data['id_asset'],
                $data['management_ip'],
                $data['ssid'],
                $this->encrypt_password($data['password']),
                $data['description'],
                $data['status_active'],
                $data['id_user'],
                $id
            ];
        } else {
            $sql = "UPDATE wifi_networks SET
                        id_asset_wifi_network = ?,
                        management_ip_wifi_network = ?,
                        ssid_wifi_network = ?,
                        description_wifi_network = ?,
                        status_active_wifi_network = ?,
                        id_user_updated_wifi_network = ?
                    WHERE id_wifi_network = ?";
            $params = [
                $data['id_asset'],
                $data['management_ip'],
                $data['ssid'],
                $data['description'],
                $data['status_active'],
                $data['id_user'],
                $id
            ];
        }
        $updated = $this->update($sql, $params);
        if ($updated) {
            $ssidChanged = ($current['ssid_wifi_network'] ?? '') !== $data['ssid'];
            $detail = $passwordChanged ? 'Registro actualizado con cambio de credenciales WiFi.' : 'Registro actualizado.';
            if ($ssidChanged || $passwordChanged) {
                $this->insert_history($id, $current['ssid_wifi_network'] ?? null, $data['ssid'], $passwordChanged ? $data['password'] : '', 'updated', $detail, $data['id_user']);
            } else {
                $this->insert_history($id, $current['ssid_wifi_network'] ?? null, $data['ssid'], '', 'updated', $detail, $data['id_user']);
            }
        }
        return $updated;
    }

    public function delete_wifi_network(int $id, int $id_user): bool
    {
        $deleted = $this->update("UPDATE wifi_networks SET status_deleted_wifi_network = 1, id_user_updated_wifi_network = ? WHERE id_wifi_network = ?", [$id_user, $id]);
        if ($deleted) {
            $this->insert_history($id, null, null, '', 'deleted', 'Registro eliminado logicamente.', $id_user);
        }
        return $deleted;
    }

    public function show_wifi_network_history(int $id): array
    {
        $sql = "SELECT h.*, CONCAT(COALESCE(pt.abbreviation_professional_title, ''), ' ', 
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
                    COALESCE(e.paternal_last_name_employee, '')) AS display_name
                FROM wifi_network_history h
                LEFT JOIN users u ON u.id_user = h.id_user_wifi_network_history
                LEFT JOIN employees e ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_wifi_network = ?
                ORDER BY h.date_created_wifi_network_history DESC";
        return $this->select_all($sql, [$id]);
    }

    private function insert_history(int $id, ?string $previousSsid, ?string $newSsid, string $password, string $action, string $detail, int $id_user): int
    {
        $fingerprint = $password !== '' ? hash('sha256', $password) : null;
        $sql = "INSERT INTO wifi_network_history(
                    id_wifi_network,
                    previous_ssid_wifi_network_history,
                    new_ssid_wifi_network_history,
                    password_fingerprint_wifi_network_history,
                    action_wifi_network_history,
                    detail_wifi_network_history,
                    id_user_wifi_network_history
                ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id, $previousSsid, $newSsid, $fingerprint, $action, $detail, $id_user]);
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