<?php
class NetworkSegmentationsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_network_segmentations(): array
    {
        $sql = "SELECT
                    ns.*,
                    COUNT(nsi.id_network_segmentation_ip) AS total_ips,
                    SUM(CASE WHEN nsi.status_network_segmentation_ip = 'available' AND nsi.status_deleted_network_segmentation_ip = 0 THEN 1 ELSE 0 END) AS available_ips,
                    SUM(CASE WHEN nsi.status_network_segmentation_ip = 'assigned' AND nsi.status_deleted_network_segmentation_ip = 0 THEN 1 ELSE 0 END) AS assigned_ips,
                    SUM(CASE WHEN nsi.status_network_segmentation_ip = 'reserved' AND nsi.status_deleted_network_segmentation_ip = 0 THEN 1 ELSE 0 END) AS reserved_ips,
                    SUM(CASE WHEN nsi.status_network_segmentation_ip = 'inactive' AND nsi.status_deleted_network_segmentation_ip = 0 THEN 1 ELSE 0 END) AS inactive_ips
                FROM network_segmentations ns
                LEFT JOIN network_segmentation_ips nsi
                    ON nsi.id_network_segmentation_network_segmentation_ip = ns.id_network_segmentation
                    AND nsi.status_deleted_network_segmentation_ip = 0
                WHERE ns.status_deleted_network_segmentation = 0
                GROUP BY ns.id_network_segmentation
                ORDER BY ns.id_network_segmentation DESC";
        return $this->select_all($sql);
    }

    public function show_network_segmentation(int $id_network_segmentation): array|false
    {
        $sql = "SELECT * FROM network_segmentations WHERE id_network_segmentation = ? AND status_deleted_network_segmentation = 0";
        return $this->select($sql, [$id_network_segmentation]);
    }

    public function insert_network_segmentation(array $data): int
    {
        $sql = "INSERT INTO network_segmentations(
                    name_network_segmentation,
                    description_network_segmentation,
                    network_address_network_segmentation,
                    subnet_mask_network_segmentation,
                    gateway_network_segmentation,
                    dns_network_segmentation,
                    ip_range_start_network_segmentation,
                    ip_range_end_network_segmentation,
                    status_active_network_segmentation
                ) VALUES (?,?,?,?,?,?,?,?,?)";
        return (int)$this->insert($sql, [
            $data['name'],
            $data['description'],
            $data['network_address'],
            $data['subnet_mask'],
            $data['gateway'],
            $data['dns'],
            $data['ip_range_start'],
            $data['ip_range_end'],
            $data['status_active']
        ]);
    }

    public function update_network_segmentation(int $id_network_segmentation, array $data): bool
    {
        $sql = "UPDATE network_segmentations SET
                    name_network_segmentation = ?,
                    description_network_segmentation = ?,
                    network_address_network_segmentation = ?,
                    subnet_mask_network_segmentation = ?,
                    gateway_network_segmentation = ?,
                    dns_network_segmentation = ?,
                    ip_range_start_network_segmentation = ?,
                    ip_range_end_network_segmentation = ?,
                    status_active_network_segmentation = ?
                WHERE id_network_segmentation = ?";
        return $this->update($sql, [
            $data['name'],
            $data['description'],
            $data['network_address'],
            $data['subnet_mask'],
            $data['gateway'],
            $data['dns'],
            $data['ip_range_start'],
            $data['ip_range_end'],
            $data['status_active'],
            $id_network_segmentation
        ]);
    }

    public function delete_network_segmentation(int $id_network_segmentation): bool
    {
        $sql = "UPDATE network_segmentations SET status_deleted_network_segmentation = 1 WHERE id_network_segmentation = ?";
        return $this->update($sql, [$id_network_segmentation]);
    }

    public function has_assigned_ips(int $id_network_segmentation): bool
    {
        $sql = "SELECT COUNT(*) AS total
                FROM network_segmentation_ips
                WHERE id_network_segmentation_network_segmentation_ip = ?
                AND status_network_segmentation_ip = 'assigned'
                AND status_deleted_network_segmentation_ip = 0";
        $row = $this->select($sql, [$id_network_segmentation]);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function ip_exists(int $id_network_segmentation, string $ip_address): bool
    {
        $sql = "SELECT id_network_segmentation_ip
                FROM network_segmentation_ips
                WHERE id_network_segmentation_network_segmentation_ip = ?
                AND ip_address_network_segmentation_ip = ?
                LIMIT 1";
        return (bool)$this->select($sql, [$id_network_segmentation, $ip_address]);
    }

    public function insert_ip(int $id_network_segmentation, string $ip_address): int
    {
        $sql = "INSERT INTO network_segmentation_ips(
                    id_network_segmentation_network_segmentation_ip,
                    ip_address_network_segmentation_ip,
                    status_network_segmentation_ip
                ) VALUES (?, ?, 'available')";
        return (int)$this->insert($sql, [$id_network_segmentation, $ip_address]);
    }

    public function mark_out_of_range_inactive(int $id_network_segmentation, string $start_ip, string $end_ip): bool
    {
        $sql = "UPDATE network_segmentation_ips
                SET status_network_segmentation_ip = 'inactive'
                WHERE id_network_segmentation_network_segmentation_ip = ?
                AND status_network_segmentation_ip <> 'assigned'
                AND status_deleted_network_segmentation_ip = 0
                AND (INET_ATON(ip_address_network_segmentation_ip) < INET_ATON(?)
                    OR INET_ATON(ip_address_network_segmentation_ip) > INET_ATON(?))";
        return $this->update($sql, [$id_network_segmentation, $start_ip, $end_ip]);
    }

    public function show_ips_by_segmentation(int $id_network_segmentation): array
    {
        $sql = "SELECT nsi.*, a.code_inventory_asset AS assigned_asset_code,
                    aeu.username_asset_equipment_user AS assigned_equipment_user,
                    CASE WHEN aia.id_asset_ip_assignment IS NOT NULL THEN 'assigned'
                         ELSE nsi.status_network_segmentation_ip END AS status_network_segmentation_ip
                FROM network_segmentation_ips nsi
                LEFT JOIN asset_ip_assignments aia
                    ON aia.id_network_segmentation_ip_asset_ip_assignment = nsi.id_network_segmentation_ip
                    AND aia.status_asset_ip_assignment = 'active'
                LEFT JOIN assets a ON a.id_asset = aia.id_asset_asset_ip_assignment
                LEFT JOIN asset_equipment_users aeu ON aeu.id_asset_asset_equipment_user = a.id_asset
                WHERE nsi.id_network_segmentation_network_segmentation_ip = ?
                AND nsi.status_deleted_network_segmentation_ip = 0
                ORDER BY INET_ATON(nsi.ip_address_network_segmentation_ip) ASC";
        return $this->select_all($sql, [$id_network_segmentation]);
    }

    public function update_ip_status(int $id_network_segmentation_ip, string $status): bool
    {
        $sql = "UPDATE network_segmentation_ips SET status_network_segmentation_ip = ? WHERE id_network_segmentation_ip = ?";
        return $this->update($sql, [$status, $id_network_segmentation_ip]);
    }

    public function available_ips(): array
    {
        $sql = "SELECT
                    nsi.id_network_segmentation_ip,
                    nsi.id_network_segmentation_network_segmentation_ip,
                    nsi.ip_address_network_segmentation_ip,
                    ns.id_network_segmentation,
                    ns.name_network_segmentation,
                    ns.network_address_network_segmentation
                FROM network_segmentation_ips nsi
                INNER JOIN network_segmentations ns
                    ON ns.id_network_segmentation = nsi.id_network_segmentation_network_segmentation_ip
                WHERE nsi.status_network_segmentation_ip IN ('available', 'reserved')
                AND nsi.status_deleted_network_segmentation_ip = 0
                AND ns.status_deleted_network_segmentation = 0
                AND ns.status_active_network_segmentation = 1
                ORDER BY ns.name_network_segmentation ASC, INET_ATON(nsi.ip_address_network_segmentation_ip) ASC";
        return $this->select_all($sql);
    }

    public function active_segmentations_for_assignment(): array
    {
        $sql = "SELECT
                    ns.id_network_segmentation,
                    ns.name_network_segmentation,
                    ns.network_address_network_segmentation,
                    COUNT(nsi.id_network_segmentation_ip) AS available_ips
                FROM network_segmentations ns
                LEFT JOIN network_segmentation_ips nsi
                    ON nsi.id_network_segmentation_network_segmentation_ip = ns.id_network_segmentation
                    AND nsi.status_network_segmentation_ip IN ('available', 'reserved')
                    AND nsi.status_deleted_network_segmentation_ip = 0
                WHERE ns.status_deleted_network_segmentation = 0
                AND ns.status_active_network_segmentation = 1
                GROUP BY ns.id_network_segmentation
                ORDER BY ns.name_network_segmentation ASC";
        return $this->select_all($sql);
    }
    public function active_interface_types(): array
    {
        return $this->select_all("SELECT id_type_network_interface, name_type_network_interface FROM types_network_interface WHERE status_active_type_network_interface = 1 AND status_deleted_type_network_interface = 0 ORDER BY name_type_network_interface");
    }

    public function valid_interface_type(int $id): bool
    {
        return (bool)$this->select("SELECT id_type_network_interface FROM types_network_interface WHERE id_type_network_interface = ? AND status_active_type_network_interface = 1 AND status_deleted_type_network_interface = 0", [$id]);
    }

    public function asset_current_ips(int $id_asset): array
    {
        $sql = "SELECT
                    aia.id_asset_ip_assignment,
                    tni.name_type_network_interface,
                    aia.observation_asset_ip_assignment,
                    aia.date_assigned_asset_ip_assignment,
                    nsi.id_network_segmentation_ip,
                    nsi.ip_address_network_segmentation_ip,
                    ns.name_network_segmentation
                FROM asset_ip_assignments aia
                LEFT JOIN types_network_interface tni ON tni.id_type_network_interface = aia.id_type_network_interface
                INNER JOIN network_segmentation_ips nsi
                    ON nsi.id_network_segmentation_ip = aia.id_network_segmentation_ip_asset_ip_assignment
                INNER JOIN network_segmentations ns
                    ON ns.id_network_segmentation = nsi.id_network_segmentation_network_segmentation_ip
                WHERE aia.id_asset_asset_ip_assignment = ?
                AND aia.status_asset_ip_assignment = 'active'
                ORDER BY aia.date_assigned_asset_ip_assignment DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function asset_ip_history(int $id_asset): array
    {
        $sql = "SELECT
                    h.*,
                    tni.name_type_network_interface,
                    nsi.ip_address_network_segmentation_ip,
                    ns.name_network_segmentation,
                    CONCAT(
                        COALESCE(pt.abbreviation_professional_title, ''), ' ',
                        COALESCE(
                            CASE e.preferred_name_employee
                                WHEN 1 THEN e.first_name_employee
                                WHEN 2 THEN e.middle_name_employee
                            END,
                            e.first_name_employee,
                            ''
                        ), ' ',
                        COALESCE(e.paternal_last_name_employee, ''), ' ',
                        COALESCE(e.maternal_last_name_employee, '')
                    ) AS display_name
                FROM asset_ip_assignment_history h
                LEFT JOIN types_network_interface tni ON tni.id_type_network_interface = h.id_type_network_interface
                INNER JOIN network_segmentation_ips nsi
                    ON nsi.id_network_segmentation_ip = h.id_network_segmentation_ip_asset_ip_assignment_history
                INNER JOIN network_segmentations ns
                    ON ns.id_network_segmentation = nsi.id_network_segmentation_network_segmentation_ip
                LEFT JOIN users u
                    ON u.id_user = h.id_user_asset_ip_assignment_history
                LEFT JOIN employees e
                    ON e.id_employee = u.id_employee_user
                LEFT JOIN professional_titles pt
                    ON pt.id_professional_title = e.id_professional_title_employee
                WHERE h.id_asset_asset_ip_assignment_history = ?
                ORDER BY h.date_created_asset_ip_assignment_history DESC";
        return $this->select_all($sql, [$id_asset]);
    }

    public function get_ip(int $id_network_segmentation_ip): array|false
    {
        $sql = "SELECT * FROM network_segmentation_ips WHERE id_network_segmentation_ip = ? AND status_deleted_network_segmentation_ip = 0";
        return $this->select($sql, [$id_network_segmentation_ip]);
    }

    public function active_assignment_exists(int $id_network_segmentation_ip): bool
    {
        $sql = "SELECT COUNT(*) AS total FROM asset_ip_assignments WHERE id_network_segmentation_ip_asset_ip_assignment = ? AND status_asset_ip_assignment = 'active'";
        $row = $this->select($sql, [$id_network_segmentation_ip]);
        return (int)($row['total'] ?? 0) > 0;
    }

    public function assign_asset_ip(int $id_asset, int $id_network_segmentation_ip, string $observation, int $id_user, int $id_type_network_interface): int
    {
        $ip = $this->get_ip($id_network_segmentation_ip);
        $previous_status = $ip['status_network_segmentation_ip'] ?? 'available';

        $sql = "INSERT INTO asset_ip_assignments(
                    id_asset_asset_ip_assignment,
                    id_network_segmentation_ip_asset_ip_assignment,
                    status_asset_ip_assignment,
                    observation_asset_ip_assignment,
                    id_type_network_interface
                ) VALUES (?, ?, 'active', ?, ?)";
        $assignment_id = (int)$this->insert($sql, [$id_asset, $id_network_segmentation_ip, $observation, $id_type_network_interface]);
        if ($assignment_id > 0) {
            $this->update_ip_status($id_network_segmentation_ip, 'assigned');
            $this->insert_history($id_asset, $id_network_segmentation_ip, 'assigned', $previous_status, 'assigned', $observation, $id_user, $id_type_network_interface);
        }
        return $assignment_id;
    }

    public function release_asset_ip(int $id_asset_ip_assignment, int $id_user, string $observation = ''): bool
    {
        $assignment = $this->select(
            "SELECT * FROM asset_ip_assignments WHERE id_asset_ip_assignment = ? AND status_asset_ip_assignment = 'active'",
            [$id_asset_ip_assignment]
        );
        if (!$assignment) {
            return false;
        }

        $id_asset = (int)$assignment['id_asset_asset_ip_assignment'];
        $id_ip = (int)$assignment['id_network_segmentation_ip_asset_ip_assignment'];
        $ip = $this->get_ip($id_ip);
        $previous_status = $ip['status_network_segmentation_ip'] ?? 'assigned';

        $updated = $this->update(
            "UPDATE asset_ip_assignments SET status_asset_ip_assignment = 'released', date_released_asset_ip_assignment = CURRENT_TIMESTAMP WHERE id_asset_ip_assignment = ?",
            [$id_asset_ip_assignment]
        );
        if ($updated) {
            $this->update_ip_status($id_ip, 'available');
            $this->insert_history($id_asset, $id_ip, 'released', $previous_status, 'available', $observation, $id_user, isset($assignment['id_type_network_interface']) ? (int)$assignment['id_type_network_interface'] : null);
        }
        return $updated;
    }

    public function insert_history(int $id_asset, int $id_network_segmentation_ip, string $action, ?string $previous_status, ?string $new_status, ?string $observation, int $id_user, ?int $id_type_network_interface = null): int
    {
        $sql = "INSERT INTO asset_ip_assignment_history(
                    id_asset_asset_ip_assignment_history,
                    id_network_segmentation_ip_asset_ip_assignment_history,
                    action_asset_ip_assignment_history,
                    previous_status_asset_ip_assignment_history,
                    new_status_asset_ip_assignment_history,
                    observation_asset_ip_assignment_history,
                    id_user_asset_ip_assignment_history,
                    id_type_network_interface
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [$id_asset, $id_network_segmentation_ip, $action, $previous_status, $new_status, $observation, $id_user, $id_type_network_interface]);
    }
}
