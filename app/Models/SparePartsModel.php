<?php
class SparePartsModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
        $this->ensure_schema();
    }

    private function ensure_schema(): void
    {
        $this->update("CREATE TABLE IF NOT EXISTS spare_parts (
            id_spare_part INT NOT NULL AUTO_INCREMENT,
            code_spare_part VARCHAR(60) NOT NULL,
            name_spare_part VARCHAR(150) NOT NULL,
            id_brand_spare_part INT DEFAULT NULL,
            model_spare_part VARCHAR(100) DEFAULT NULL,
            unit_spare_part VARCHAR(30) NOT NULL DEFAULT 'unidad',
            stock_current_spare_part DECIMAL(10,2) NOT NULL DEFAULT 0,
            stock_minimum_spare_part DECIMAL(10,2) NOT NULL DEFAULT 0,
            description_spare_part TEXT NULL,
            status_active_spare_part TINYINT NOT NULL DEFAULT 1,
            status_deleted_spare_part TINYINT NOT NULL DEFAULT 0,
            date_created_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            date_updated_spare_part TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id_spare_part),
            UNIQUE KEY uk_spare_parts_code (code_spare_part),
            KEY idx_spare_parts_brand (id_brand_spare_part)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->ensure_brand_column();

        $this->update("CREATE TABLE IF NOT EXISTS spare_part_movements (
            id_spare_part_movement INT NOT NULL AUTO_INCREMENT,
            id_spare_part INT NOT NULL,
            id_maintenance_corrective INT DEFAULT NULL,
            type_spare_part_movement ENUM('IN','OUT','ADJUSTMENT') NOT NULL,
            quantity_spare_part_movement DECIMAL(10,2) NOT NULL DEFAULT 0,
            reason_spare_part_movement TEXT NULL,
            stock_after_spare_part_movement DECIMAL(10,2) NOT NULL DEFAULT 0,
            date_created_spare_part_movement TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id_spare_part_movement),
            KEY idx_spm_spare_part (id_spare_part),
            KEY idx_spm_maintenance (id_maintenance_corrective)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    private function ensure_brand_column(): void
    {
        $column = $this->select("SHOW COLUMNS FROM spare_parts LIKE 'id_brand_spare_part'");
        if (!$column) {
            $this->update("ALTER TABLE spare_parts ADD COLUMN id_brand_spare_part INT DEFAULT NULL AFTER name_spare_part");
        }

        $index = $this->select("SHOW INDEX FROM spare_parts WHERE Key_name = 'idx_spare_parts_brand'");
        if (!$index) {
            $this->update("ALTER TABLE spare_parts ADD KEY idx_spare_parts_brand (id_brand_spare_part)");
        }

        $legacy_column = $this->select("SHOW COLUMNS FROM spare_parts LIKE 'brand_spare_part'");
        if ($legacy_column) {
            $this->update("UPDATE spare_parts sp INNER JOIN brands b ON LOWER(TRIM(b.name_brand)) = LOWER(TRIM(sp.brand_spare_part)) SET sp.id_brand_spare_part = b.id_brand WHERE sp.id_brand_spare_part IS NULL");
            $this->update("ALTER TABLE spare_parts DROP COLUMN brand_spare_part");
        }
    }

    public function show_spare_parts(): array
    {
        return $this->select_all(
            "SELECT sp.*, b.name_brand AS name_brand_spare_part,
                    COALESCE(b.name_brand, 'Ninguna.') AS brand_spare_part
             FROM spare_parts sp
             LEFT JOIN brands b ON b.id_brand = sp.id_brand_spare_part
             WHERE sp.status_deleted_spare_part = 0
             ORDER BY sp.id_spare_part DESC"
        );
    }

    public function show_spare_part(int $id_spare_part)
    {
        return $this->select(
            "SELECT sp.*, b.name_brand AS name_brand_spare_part,
                    COALESCE(b.name_brand, 'Ninguna.') AS brand_spare_part
             FROM spare_parts sp
             LEFT JOIN brands b ON b.id_brand = sp.id_brand_spare_part
             WHERE sp.id_spare_part = ? AND sp.status_deleted_spare_part = 0",
            [$id_spare_part]
        );
    }

    public function show_brand(int $id_brand)
    {
        return $this->select(
            "SELECT id_brand, name_brand FROM brands WHERE id_brand = ? AND status_active_brand = 1 AND status_deleted_brand = 0",
            [$id_brand]
        );
    }

    public function combo(): array
    {
        return $this->select_all("SELECT id_spare_part, code_spare_part, name_spare_part, unit_spare_part, stock_current_spare_part FROM spare_parts WHERE status_active_spare_part = 1 AND status_deleted_spare_part = 0 ORDER BY name_spare_part ASC");
    }

    public function insert_spare_part(
        string $name,
        int $id_brand,
        string $model,
        string $unit,
        float $stock_current,
        float $stock_minimum,
        string $description,
        int $status_active
    ) {
        $code = 'REP-' . date('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
        $id = $this->insert(
            "INSERT INTO spare_parts(code_spare_part, name_spare_part, id_brand_spare_part, model_spare_part, unit_spare_part, stock_current_spare_part, stock_minimum_spare_part, description_spare_part, status_active_spare_part) VALUES(?,?,?,?,?,?,?,?,?)",
            [$code, $name, $id_brand, $model, $unit, $stock_current, $stock_minimum, $description, $status_active]
        );
        if ($id && $stock_current > 0) {
            $this->insert(
                "INSERT INTO spare_part_movements(id_spare_part, type_spare_part_movement, quantity_spare_part_movement, reason_spare_part_movement, stock_after_spare_part_movement) VALUES(?,?,?,?,?)",
                [$id, 'IN', $stock_current, 'Stock inicial', $stock_current]
            );
        }
        return $id;
    }

    public function update_spare_part(
        int $id_spare_part,
        string $name,
        int $id_brand,
        string $model,
        string $unit,
        float $stock_minimum,
        string $description,
        int $status_active
    ) {
        return $this->update(
            "UPDATE spare_parts SET name_spare_part = ?, id_brand_spare_part = ?, model_spare_part = ?, unit_spare_part = ?, stock_minimum_spare_part = ?, description_spare_part = ?, status_active_spare_part = ? WHERE id_spare_part = ?",
            [$name, $id_brand, $model, $unit, $stock_minimum, $description, $status_active, $id_spare_part]
        );
    }

    public function register_stock_movement(int $id_spare_part, string $type, float $quantity, string $reason)
    {
        $spare = $this->show_spare_part($id_spare_part);
        if (!$spare || $quantity <= 0) {
            return false;
        }
        $stock_current = floatval($spare['stock_current_spare_part']);
        if ($type === 'IN') {
            $stock_after = $stock_current + $quantity;
        } elseif ($type === 'OUT') {
            if ($stock_current < $quantity) {
                return false;
            }
            $stock_after = $stock_current - $quantity;
        } else {
            $stock_after = $quantity;
            $quantity = abs($stock_after - $stock_current);
        }

        $this->update("UPDATE spare_parts SET stock_current_spare_part = ? WHERE id_spare_part = ?", [$stock_after, $id_spare_part]);
        return $this->insert(
            "INSERT INTO spare_part_movements(id_spare_part, type_spare_part_movement, quantity_spare_part_movement, reason_spare_part_movement, stock_after_spare_part_movement) VALUES(?,?,?,?,?)",
            [$id_spare_part, $type, $quantity, $reason, $stock_after]
        );
    }

    public function show_movements(int $id_spare_part): array
    {
        return $this->select_all(
            "SELECT * FROM spare_part_movements WHERE id_spare_part = ? ORDER BY id_spare_part_movement DESC",
            [$id_spare_part]
        );
    }

    public function delete_spare_part(int $id_spare_part)
    {
        return $this->update("UPDATE spare_parts SET status_deleted_spare_part = 1 WHERE id_spare_part = ?", [$id_spare_part]);
    }
}
