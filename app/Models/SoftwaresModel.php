<?php
class SoftwaresModel extends Mysql
{
    public function asset(int $id): array|false
    {
        return $this->select('SELECT a.id_asset, a.code_inventory_asset, t.name_type_asset FROM assets a LEFT JOIN types_asset t ON t.id_type_asset=a.id_type_asset_asset WHERE a.id_asset=? AND a.status_deleted_asset=0', [$id]);
    }
    public function show_softwares_by_asset(int $id): array
    {
        return $this->select_all('SELECT s.*, l.name_type_license, t.name_type_software, a.name_architecture FROM softwares s LEFT JOIN types_license l ON l.id_type_license=s.id_type_license_software LEFT JOIN types_software t ON t.id_type_software=s.id_type_software_software LEFT JOIN architectures a ON a.id_architecture=s.id_architecture_software WHERE s.id_asset_software=? AND s.status_deleted_software=0 ORDER BY s.id_software DESC', [$id]);
    }
    public function software(int $id, int $asset): array|false
    {
        return $this->select('SELECT * FROM softwares WHERE id_software=? AND id_asset_software=? AND status_deleted_software=0', [$id, $asset]);
    }
    public function catalogs(): array
    {
        return [
            'licenses'=>$this->select_all('SELECT id_type_license AS id, name_type_license AS name FROM types_license WHERE status_active_type_license=1 AND status_deleted_type_license=0 ORDER BY name_type_license'),
            'types'=>$this->select_all('SELECT id_type_software AS id, name_type_software AS name FROM types_software WHERE status_active_type_software=1 AND status_deleted_type_software=0 ORDER BY name_type_software'),
            'architectures'=>$this->select_all('SELECT id_architecture AS id, name_architecture AS name FROM architectures WHERE status_active_architecture=1 AND status_deleted_architecture=0 ORDER BY name_architecture')
        ];
    }
    public function save_software(int $asset, int $id, array $data): bool
    {
        $values=[$data['id_type_license_software'], $data['id_type_software_software'], $data['id_architecture_software'], $data['name_software'], $data['version_software'], $data['description_software']];
        if ($id > 0) {
            return $this->update('UPDATE softwares SET id_type_license_software=?, id_type_software_software=?, id_architecture_software=?, name_software=?, version_software=?, description_software=? WHERE id_software=? AND id_asset_software=? AND status_deleted_software=0', array_merge($values, [$id,$asset]));
        }
        return (int)$this->insert('INSERT INTO softwares (id_type_license_software,id_type_software_software,id_architecture_software,name_software,version_software,description_software,id_asset_software) VALUES (?,?,?,?,?,?,?)', array_merge($values,[$asset])) > 0;
    }
    public function delete_software(int $id, int $asset): bool
    {
        return $this->update('UPDATE softwares SET status_deleted_software=1 WHERE id_software=? AND id_asset_software=? AND status_deleted_software=0',[$id,$asset]);
    }
}
