<?php
class TrashModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    public function list_deleted_records(array $config): array
    {
        $table = $config['table'];
        $id_column = $config['id_column'];
        $label_column = $config['label_column'];
        $status_deleted_column = $config['status_deleted_column'];
        $created_column = $config['created_column'];

        $sql = "SELECT {$id_column} AS id_record,
                       {$label_column} AS label_record,
                       {$created_column} AS created_record
                FROM {$table}
                WHERE {$status_deleted_column} = 1
                ORDER BY {$id_column} DESC
                LIMIT 250";
        return $this->select_all($sql);
    }

    public function restore_record(array $config, int $id_record): bool
    {
        $table = $config['table'];
        $id_column = $config['id_column'];
        $status_deleted_column = $config['status_deleted_column'];

        $sql = "UPDATE {$table}
                SET {$status_deleted_column} = 0
                WHERE {$id_column} = ?";
        return (bool)$this->update($sql, [$id_record]);
    }

    public function hard_delete_record(array $config, int $id_record): array
    {
        $table = $config['table'];
        $id_column = $config['id_column'];

        try {
            if (!empty($config['related_delete']) && is_array($config['related_delete'])) {
                foreach ($config['related_delete'] as $rel) {
                    if (!empty($rel['sql'])) {
                        $sql_custom = (string)$rel['sql'];
                        $this->delete($sql_custom, [$id_record]);
                        continue;
                    }

                    $child_table = $rel['table'];
                    $foreign_column = $rel['foreign_column'];
                    $sql_child = "DELETE FROM {$child_table} WHERE {$foreign_column} = ?";
                    $this->delete($sql_child, [$id_record]);
                }
            }

            $sql = "DELETE FROM {$table} WHERE {$id_column} = ?";
            $request = $this->delete($sql, [$id_record]);
            if ($request) {
                return ["status" => true, "message" => "ok"];
            }
            return ["status" => false, "message" => "No se pudo eliminar el registro."];
        } catch (Throwable $th) {
            return [
                "status" => false,
                "message" => "No se pudo eliminar definitivamente por integridad de datos."
            ];
        }
    }
}
?>
