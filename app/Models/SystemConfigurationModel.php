<?php
    class SystemConfigurationModel extends Mysql
    {
        public function __construct()
        {
            parent::__construct();
        }

        public function settings(): array
        {
            $rows = $this->select_all("SELECT setting_key, setting_value FROM system_settings");
            $settings = [];
            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            return $settings;
        }

        public function public_access_status(): array
        {
            $settings = $this->settings();
            $enabled = (int)($settings['public_quick_access_enabled'] ?? 0) === 1;
            $mode = $settings['public_quick_access_mode'] ?? 'indeterminate';
            $starts = trim((string)($settings['public_quick_access_starts_at'] ?? ''));
            $expires = trim((string)($settings['public_quick_access_expires_at'] ?? ''));
            $now = time();

            $status = 'Deshabilitado';
            $isActive = false;
            if ($enabled) {
                if ($mode === 'timed') {
                    $startTs = $starts !== '' ? strtotime($starts) : false;
                    $expiresTs = $expires !== '' ? strtotime($expires) : false;
                    if ($expiresTs && $now > $expiresTs) {
                        $status = 'Expirado';
                    } elseif ($startTs && $now < $startTs) {
                        $status = 'Programado';
                    } else {
                        $status = 'Activo';
                        $isActive = true;
                    }
                } else {
                    $status = 'Activo';
                    $isActive = true;
                }
            }

            return [
                'enabled' => $enabled,
                'mode' => $mode,
                'starts_at' => $starts,
                'expires_at' => $expires,
                'status' => $status,
                'is_active' => $isActive
            ];
        }

        public function update_public_access(array $payload, int $id_user): bool
        {
            $updates = [
                'public_quick_access_enabled' => (string)$payload['enabled'],
                'public_quick_access_mode' => $payload['mode'],
                'public_quick_access_starts_at' => $payload['starts_at'],
                'public_quick_access_expires_at' => $payload['expires_at']
            ];

            foreach ($updates as $key => $value) {
                $this->upsert_setting($key, $value, 'public_access');
            }
            return true;
        }

        private function upsert_setting(string $key, string $value, string $group): bool
        {
            $sql = "INSERT INTO system_settings(setting_key, setting_value, setting_group)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), setting_group = VALUES(setting_group)";
            return $this->update($sql, [$key, $value, $group]);
        }
    }
?>
