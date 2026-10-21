<?php
class LabelTemplatesModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function show_label_templates(): array
    {
        $sql = "SELECT * FROM label_templates
                WHERE status_deleted_label_template = 0
                ORDER BY id_label_template DESC";
        return $this->select_all($sql);
    }

    public function active_label_templates(): array
    {
        $sql = "SELECT id_label_template, name_label_template, width_cm_label_template, height_cm_label_template,
                       design_route_label_template, qr_x_percent_label_template, qr_y_percent_label_template,
                       qr_width_percent_label_template, qr_height_percent_label_template,
                       code_x_percent_label_template, code_y_percent_label_template,
                       code_width_percent_label_template, code_height_percent_label_template,
                       code_font_size_label_template
                FROM label_templates
                WHERE status_deleted_label_template = 0 AND status_active_label_template = 1
                ORDER BY name_label_template ASC";
        return $this->select_all($sql);
    }

    public function show_label_template(int $id): array|false
    {
        $sql = "SELECT * FROM label_templates WHERE id_label_template = ? AND status_deleted_label_template = 0";
        return $this->select($sql, [$id]);
    }

    public function insert_label_template(array $data): int
    {
        $sql = "INSERT INTO label_templates(
                    name_label_template,
                    width_cm_label_template,
                    height_cm_label_template,
                    description_label_template,
                    status_active_label_template,
                    design_route_label_template,
                    qr_x_percent_label_template,
                    qr_y_percent_label_template,
                    qr_width_percent_label_template,
                    qr_height_percent_label_template,
                    code_x_percent_label_template,
                    code_y_percent_label_template,
                    code_width_percent_label_template,
                    code_height_percent_label_template,
                    code_font_size_label_template
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        return (int)$this->insert($sql, [
            $data['name'],
            $data['width_cm'],
            $data['height_cm'],
            $data['description'],
            $data['status_active'],
            $data['design_route'],
            $data['qr_x'],
            $data['qr_y'],
            $data['qr_width'],
            $data['qr_height'],
            $data['code_x'],
            $data['code_y'],
            $data['code_width'],
            $data['code_height'],
            $data['code_font_size']
        ]);
    }

    public function update_label_template(int $id, array $data): bool
    {
        if (!empty($data['design_route'])) {
            $sql = "UPDATE label_templates SET
                        name_label_template = ?,
                        width_cm_label_template = ?,
                        height_cm_label_template = ?,
                        description_label_template = ?,
                        status_active_label_template = ?,
                        design_route_label_template = ?,
                        qr_x_percent_label_template = ?,
                        qr_y_percent_label_template = ?,
                        qr_width_percent_label_template = ?,
                        qr_height_percent_label_template = ?,
                        code_x_percent_label_template = ?,
                        code_y_percent_label_template = ?,
                        code_width_percent_label_template = ?,
                        code_height_percent_label_template = ?,
                        code_font_size_label_template = ?
                    WHERE id_label_template = ?";
            $params = [
                $data['name'], $data['width_cm'], $data['height_cm'], $data['description'],
                $data['status_active'], $data['design_route'], $data['qr_x'], $data['qr_y'],
                $data['qr_width'], $data['qr_height'], $data['code_x'], $data['code_y'],
                $data['code_width'], $data['code_height'], $data['code_font_size'], $id
            ];
        } else {
            $sql = "UPDATE label_templates SET
                        name_label_template = ?,
                        width_cm_label_template = ?,
                        height_cm_label_template = ?,
                        description_label_template = ?,
                        status_active_label_template = ?,
                        qr_x_percent_label_template = ?,
                        qr_y_percent_label_template = ?,
                        qr_width_percent_label_template = ?,
                        qr_height_percent_label_template = ?,
                        code_x_percent_label_template = ?,
                        code_y_percent_label_template = ?,
                        code_width_percent_label_template = ?,
                        code_height_percent_label_template = ?,
                        code_font_size_label_template = ?
                    WHERE id_label_template = ?";
            $params = [
                $data['name'], $data['width_cm'], $data['height_cm'], $data['description'],
                $data['status_active'], $data['qr_x'], $data['qr_y'], $data['qr_width'],
                $data['qr_height'], $data['code_x'], $data['code_y'], $data['code_width'],
                $data['code_height'], $data['code_font_size'], $id
            ];
        }
        return $this->update($sql, $params);
    }

    public function delete_label_template(int $id): bool
    {
        return $this->update("UPDATE label_templates SET status_deleted_label_template = 1 WHERE id_label_template = ?", [$id]);
    }
}