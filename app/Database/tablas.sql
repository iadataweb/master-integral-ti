SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

-- Tabla: additional_permissions
CREATE TABLE `additional_permissions` (
  `id_additional_permission` int NOT NULL AUTO_INCREMENT,
  `id_role_additional_permission` int NOT NULL,
  `id_control_additional_permission` int NOT NULL,
  `allow_additional_permission` tinyint NOT NULL DEFAULT '0',
  `date_created_additional_permission` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_additional_permission` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_additional_permission`),
  UNIQUE KEY `uq_additional_role_control` (`id_role_additional_permission`,`id_control_additional_permission`),
  KEY `id_role_additional_permission` (`id_role_additional_permission`),
  KEY `id_control_additional_permission` (`id_control_additional_permission`),
  CONSTRAINT `additional_permissions_ibfk_1` FOREIGN KEY (`id_role_additional_permission`) REFERENCES `roles` (`id_role`),
  CONSTRAINT `additional_permissions_ibfk_2` FOREIGN KEY (`id_control_additional_permission`) REFERENCES `controls` (`id_control`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: architectures
CREATE TABLE `architectures` (
  `id_architecture` int NOT NULL AUTO_INCREMENT,
  `name_architecture` varchar(100) NOT NULL,
  `status_active_architecture` tinyint NOT NULL DEFAULT '1',
  `status_deleted_architecture` tinyint NOT NULL DEFAULT '0',
  `date_created_architecture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_architecture` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_architecture`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: areas
CREATE TABLE `areas` (
  `id_area` int NOT NULL AUTO_INCREMENT,
  `name_area` varchar(100) NOT NULL,
  `status_active_area` tinyint NOT NULL DEFAULT '1',
  `status_deleted_area` tinyint NOT NULL DEFAULT '0',
  `date_created_area` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_area` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_area`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: asset_confidential_notes
CREATE TABLE `asset_confidential_notes` (
  `id_asset_confidential_note` int NOT NULL AUTO_INCREMENT,
  `id_asset_asset_confidential_note` int NOT NULL,
  `confidential_note_asset` text COLLATE utf8mb4_general_ci,
  `id_user_created_asset_confidential_note` int DEFAULT NULL,
  `id_user_updated_asset_confidential_note` int DEFAULT NULL,
  `date_created_asset_confidential_note` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_asset_confidential_note` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asset_confidential_note`),
  UNIQUE KEY `uq_asset_confidential_note_asset` (`id_asset_asset_confidential_note`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabla: asset_email_account_assignment_history
CREATE TABLE `asset_email_account_assignment_history` (
  `id_asset_email_account_assignment_history` int NOT NULL AUTO_INCREMENT,
  `id_asset` int NOT NULL,
  `id_email_account` int NOT NULL,
  `action_asset_email_account_assignment_history` enum('assigned','released') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation_asset_email_account_assignment_history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_user` int DEFAULT NULL,
  `date_created_asset_email_account_assignment_history` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asset_email_account_assignment_history`),
  KEY `idx_aea_history_asset` (`id_asset`),
  KEY `idx_aea_history_email` (`id_email_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: asset_email_account_assignments
CREATE TABLE `asset_email_account_assignments` (
  `id_asset_email_account_assignment` int NOT NULL AUTO_INCREMENT,
  `id_asset` int NOT NULL,
  `id_email_account` int NOT NULL,
  `status_asset_email_account_assignment` enum('active','released') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `observation_asset_email_account_assignment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_assigned_asset_email_account_assignment` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_released_asset_email_account_assignment` datetime DEFAULT NULL,
  `id_user_assigned` int DEFAULT NULL,
  `id_user_released` int DEFAULT NULL,
  PRIMARY KEY (`id_asset_email_account_assignment`),
  KEY `idx_aea_asset` (`id_asset`),
  KEY `idx_aea_email` (`id_email_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: asset_equipment_users
CREATE TABLE `asset_equipment_users` (
  `id_asset_equipment_user` int NOT NULL AUTO_INCREMENT,
  `id_asset_asset_equipment_user` int NOT NULL,
  `username_asset_equipment_user` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation_asset_equipment_user` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_user_created_asset_equipment_user` int DEFAULT NULL,
  `id_user_updated_asset_equipment_user` int DEFAULT NULL,
  `date_created_asset_equipment_user` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_asset_equipment_user` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asset_equipment_user`),
  UNIQUE KEY `uq_asset_equipment_user_asset` (`id_asset_asset_equipment_user`),
  KEY `idx_asset_equipment_user_asset` (`id_asset_asset_equipment_user`),
  CONSTRAINT `fk_asset_equipment_users_asset` FOREIGN KEY (`id_asset_asset_equipment_user`) REFERENCES `assets` (`id_asset`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: asset_ip_assignment_history
CREATE TABLE `asset_ip_assignment_history` (
  `id_type_network_interface` int DEFAULT NULL,
  `id_asset_ip_assignment_history` int NOT NULL AUTO_INCREMENT,
  `id_asset_asset_ip_assignment_history` int NOT NULL,
  `id_network_segmentation_ip_asset_ip_assignment_history` int NOT NULL,
  `action_asset_ip_assignment_history` varchar(40) NOT NULL,
  `previous_status_asset_ip_assignment_history` varchar(40) DEFAULT NULL,
  `new_status_asset_ip_assignment_history` varchar(40) DEFAULT NULL,
  `observation_asset_ip_assignment_history` text,
  `id_user_asset_ip_assignment_history` int DEFAULT NULL,
  `date_created_asset_ip_assignment_history` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asset_ip_assignment_history`),
  KEY `idx_asset_ip_assignment_history_interface_type` (`id_type_network_interface`),
  CONSTRAINT `fk_asset_ip_assignment_history_interface_type` FOREIGN KEY (`id_type_network_interface`) REFERENCES `types_network_interface` (`id_type_network_interface`),
  KEY `idx_asset_ip_history_asset` (`id_asset_asset_ip_assignment_history`),
  KEY `idx_asset_ip_history_ip` (`id_network_segmentation_ip_asset_ip_assignment_history`),
  CONSTRAINT `fk_asset_ip_history_asset` FOREIGN KEY (`id_asset_asset_ip_assignment_history`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `fk_asset_ip_history_ip` FOREIGN KEY (`id_network_segmentation_ip_asset_ip_assignment_history`) REFERENCES `network_segmentation_ips` (`id_network_segmentation_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: asset_ip_assignments
CREATE TABLE `asset_ip_assignments` (
  `id_type_network_interface` int DEFAULT NULL,
  `id_asset_ip_assignment` int NOT NULL AUTO_INCREMENT,
  `id_asset_asset_ip_assignment` int NOT NULL,
  `id_network_segmentation_ip_asset_ip_assignment` int NOT NULL,
  `status_asset_ip_assignment` enum('active','released') NOT NULL DEFAULT 'active',
  `observation_asset_ip_assignment` text,
  `date_assigned_asset_ip_assignment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_released_asset_ip_assignment` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_asset_ip_assignment`),
  KEY `idx_asset_ip_assignments_interface_type` (`id_type_network_interface`),
  CONSTRAINT `fk_asset_ip_assignments_interface_type` FOREIGN KEY (`id_type_network_interface`) REFERENCES `types_network_interface` (`id_type_network_interface`),
  KEY `idx_asset_ip_assignment_asset` (`id_asset_asset_ip_assignment`),
  KEY `idx_asset_ip_assignment_ip` (`id_network_segmentation_ip_asset_ip_assignment`),
  CONSTRAINT `fk_asset_ip_assignments_asset` FOREIGN KEY (`id_asset_asset_ip_assignment`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `fk_asset_ip_assignments_ip` FOREIGN KEY (`id_network_segmentation_ip_asset_ip_assignment`) REFERENCES `network_segmentation_ips` (`id_network_segmentation_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: asset_movement_registers
CREATE TABLE `asset_movement_registers` (
  `id_asset_movement_register` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_asset` int NOT NULL,
  PRIMARY KEY (`id_asset_movement_register`),
  UNIQUE KEY `uq_movement_register_asset` (`id_asset`),
  CONSTRAINT `fk_movement_register_asset` FOREIGN KEY (`id_asset`) REFERENCES `assets` (`id_asset`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: asset_windows_credential_assignment_history
CREATE TABLE `asset_windows_credential_assignment_history` (
  `id_asset_windows_credential_assignment_history` int NOT NULL AUTO_INCREMENT,
  `id_asset` int NOT NULL,
  `id_windows_credential` int NOT NULL,
  `action_asset_windows_credential_assignment_history` enum('assigned','released') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation_asset_windows_credential_assignment_history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_user` int DEFAULT NULL,
  `date_created_asset_windows_credential_assignment_history` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_asset_windows_credential_assignment_history`),
  KEY `idx_awc_history_asset` (`id_asset`),
  KEY `idx_awc_history_credential` (`id_windows_credential`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: asset_windows_credential_assignments
CREATE TABLE `asset_windows_credential_assignments` (
  `id_asset_windows_credential_assignment` int NOT NULL AUTO_INCREMENT,
  `id_asset_asset_windows_credential_assignment` int NOT NULL,
  `id_windows_credential_asset_windows_credential_assignment` int NOT NULL,
  `status_asset_windows_credential_assignment` enum('active','released') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `observation_asset_windows_credential_assignment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_assigned_asset_windows_credential_assignment` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_released_asset_windows_credential_assignment` datetime DEFAULT NULL,
  `id_user_assigned_asset_windows_credential_assignment` int DEFAULT NULL,
  `id_user_released_asset_windows_credential_assignment` int DEFAULT NULL,
  PRIMARY KEY (`id_asset_windows_credential_assignment`),
  KEY `idx_asset_windows_credential_asset` (`id_asset_asset_windows_credential_assignment`),
  KEY `idx_asset_windows_credential_credential` (`id_windows_credential_asset_windows_credential_assignment`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: assets
CREATE TABLE `assets` (
  `id_asset` int NOT NULL AUTO_INCREMENT,
  `id_category_asset_asset` int NOT NULL,
  `id_type_asset_asset` int NOT NULL,
  `id_brand_asset` int NOT NULL,
  `id_condition_asset_asset` int NOT NULL,
  `code_inventory_asset` varchar(100) NOT NULL,
  `model_asset` varchar(100) DEFAULT NULL,
  `serial_number_asset` varchar(100) DEFAULT NULL,
  `entry_date_asset` date DEFAULT NULL,
  `acquisition_date_asset` date DEFAULT NULL,
  `descriptions_asset` text,
  `observations_asset` text,
  `status_movement_asset` tinyint NOT NULL DEFAULT '0',
  `status_active_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status_maintenance_asset` tinyint NOT NULL DEFAULT '0',
  `label_attached_asset` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_asset`),
  UNIQUE KEY `code_inventory_asset` (`code_inventory_asset`),
  KEY `id_category_asset_asset` (`id_category_asset_asset`),
  KEY `id_type_asset_asset` (`id_type_asset_asset`),
  KEY `id_brand_asset` (`id_brand_asset`),
  KEY `id_condition_asset_asset` (`id_condition_asset_asset`),
  CONSTRAINT `assets_ibfk_1` FOREIGN KEY (`id_category_asset_asset`) REFERENCES `categories_asset` (`id_category_asset`),
  CONSTRAINT `assets_ibfk_2` FOREIGN KEY (`id_type_asset_asset`) REFERENCES `types_asset` (`id_type_asset`),
  CONSTRAINT `assets_ibfk_3` FOREIGN KEY (`id_brand_asset`) REFERENCES `brands` (`id_brand`),
  CONSTRAINT `assets_ibfk_4` FOREIGN KEY (`id_condition_asset_asset`) REFERENCES `conditions_asset` (`id_condition_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_asset
CREATE TABLE `attachments_asset` (
  `id_attachment_asset` int NOT NULL AUTO_INCREMENT,
  `id_asset` int NOT NULL,
  `route_attachment_asset` varchar(255) NOT NULL,
  `name_attachment_asset` text NOT NULL,
  `status_open_attachment_asset` tinyint NOT NULL DEFAULT '1',
  `status_active_attachment_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_asset`),
  KEY `id_asset` (`id_asset`),
  CONSTRAINT `attachments_asset_ibfk_1` FOREIGN KEY (`id_asset`) REFERENCES `assets` (`id_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_history_maintenance_preventive
CREATE TABLE `attachments_history_maintenance_preventive` (
  `id_attachment_history_maintenance_preventive` int NOT NULL AUTO_INCREMENT,
  `id_history_maintenance_preventive` int NOT NULL,
  `type_attachment_history_maintenance_preventive` varchar(20) NOT NULL,
  `route_attachment_history_maintenance_preventive` varchar(255) NOT NULL,
  `name_attachment_history_maintenance_preventive` varchar(255) NOT NULL,
  `status_active_attachment_history_maintenance_preventive` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_history_maintenance_preventive` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_history_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_history_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_history_maintenance_preventive`),
  KEY `idx_ahmp_history` (`id_history_maintenance_preventive`),
  CONSTRAINT `fk_ahmp_history` FOREIGN KEY (`id_history_maintenance_preventive`) REFERENCES `history_maintenances_preventive` (`id_history_maintenance_preventive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_incident
CREATE TABLE `attachments_incident` (
  `id_attachment_incident` int NOT NULL AUTO_INCREMENT,
  `id_incident_attachment_incident` int NOT NULL,
  `route_attachment_incident` varchar(255) NOT NULL,
  `name_attachment_incident` text NOT NULL,
  `status_open_attachment_incident` tinyint NOT NULL DEFAULT '1',
  `status_active_attachment_incident` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_incident` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_incident`),
  KEY `idx_attachment_incident` (`id_incident_attachment_incident`),
  CONSTRAINT `attachments_incident_ibfk_1` FOREIGN KEY (`id_incident_attachment_incident`) REFERENCES `incidents` (`id_incident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_maintenance_corrective
CREATE TABLE `attachments_maintenance_corrective` (
  `id_attachment_maintenance_corrective` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_corrective` int NOT NULL,
  `route_attachment_maintenance_corrective` varchar(255) NOT NULL,
  `name_attachment_maintenance_corrective` varchar(255) NOT NULL,
  `status_active_attachment_maintenance_corrective` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_maintenance_corrective` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_maintenance_corrective`),
  KEY `idx_amc_maintenance` (`id_maintenance_corrective`),
  CONSTRAINT `fk_amc_maintenance` FOREIGN KEY (`id_maintenance_corrective`) REFERENCES `maintenances_corrective` (`id_maintenance_corrective`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_maintenance_preventive
CREATE TABLE `attachments_maintenance_preventive` (
  `id_attachment_maintenance_preventive` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_preventive` int NOT NULL,
  `route_attachment_maintenance_preventive` varchar(255) NOT NULL,
  `name_attachment_maintenance_preventive` text NOT NULL,
  `status_open_attachment_maintenance_preventive` tinyint NOT NULL DEFAULT '1',
  `status_active_attachment_maintenance_preventive` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_maintenance_preventive` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_maintenance_preventive`),
  KEY `id_maintenance_preventive` (`id_maintenance_preventive`),
  CONSTRAINT `attachments_maintenance_preventive_ibfk_1` FOREIGN KEY (`id_maintenance_preventive`) REFERENCES `maintenances_preventive` (`id_maintenance_preventive`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: attachments_movement_asset
CREATE TABLE `attachments_movement_asset` (
  `id_attachment_movement_asset` int NOT NULL AUTO_INCREMENT,
  `id_movement_asset` int NOT NULL,
  `route_attachment_movement_asset` varchar(255) NOT NULL,
  `name_attachment_movement_asset` text NOT NULL,
  `status_open_attachment_movement_asset` tinyint NOT NULL DEFAULT '1',
  `status_active_attachment_movement_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_attachment_movement_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_attachment_movement_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_attachment_movement_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_attachment_movement_asset`),
  KEY `id_movement_asset` (`id_movement_asset`),
  CONSTRAINT `attachments_movement_asset_ibfk_1` FOREIGN KEY (`id_movement_asset`) REFERENCES `movements_asset` (`id_movement_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: brands
CREATE TABLE `brands` (
  `id_brand` int NOT NULL AUTO_INCREMENT,
  `name_brand` varchar(100) NOT NULL,
  `status_active_brand` tinyint NOT NULL DEFAULT '1',
  `status_deleted_brand` tinyint NOT NULL DEFAULT '0',
  `date_created_brand` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_brand` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_brand`),
  UNIQUE KEY `name_brand` (`name_brand`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: campuses
CREATE TABLE `campuses` (
  `id_campus` int NOT NULL AUTO_INCREMENT,
  `name_campus` varchar(100) NOT NULL,
  `address_campus` varchar(100) NOT NULL,
  `status_active_campus` tinyint NOT NULL DEFAULT '1',
  `status_deleted_campus` tinyint NOT NULL DEFAULT '0',
  `date_created_campus` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_campus` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_campus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: categories_asset
CREATE TABLE `categories_asset` (
  `id_category_asset` int NOT NULL AUTO_INCREMENT,
  `name_category_asset` varchar(100) NOT NULL,
  `status_active_category_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_category_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_category_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_category_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_category_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: companies
CREATE TABLE `companies` (
  `id_company` int NOT NULL AUTO_INCREMENT,
  `name_company` varchar(100) NOT NULL,
  `ruc_company` varchar(12) DEFAULT NULL,
  `cell_phone_company` varchar(9) DEFAULT NULL,
  `telephone_company` varchar(10) DEFAULT NULL,
  `email_company` varchar(200) DEFAULT NULL,
  `description_company` text,
  `observation_company` text NOT NULL,
  `status_active_company` tinyint NOT NULL DEFAULT '1',
  `status_deleted_company` tinyint NOT NULL DEFAULT '0',
  `date_created_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: conditions_asset
CREATE TABLE `conditions_asset` (
  `id_condition_asset` int NOT NULL AUTO_INCREMENT,
  `name_condition_asset` varchar(100) NOT NULL,
  `status_active_condition_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_condition_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_condition_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_condition_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_condition_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: controls
CREATE TABLE `controls` (
  `id_control` int NOT NULL AUTO_INCREMENT,
  `id_module_control` int NOT NULL,
  `name_control` text NOT NULL,
  `description_control` text NOT NULL,
  `status_active_control` tinyint NOT NULL DEFAULT '1',
  `status_deleted_control` tinyint NOT NULL DEFAULT '0',
  `date_created_control` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_control` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_control`),
  KEY `id_module_control` (`id_module_control`),
  CONSTRAINT `controls_ibfk_1` FOREIGN KEY (`id_module_control`) REFERENCES `modules` (`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: email_account_password_history
CREATE TABLE `email_account_password_history` (
  `id_email_account_password_history` int NOT NULL AUTO_INCREMENT,
  `id_email_account` int NOT NULL,
  `password_fingerprint_email_account_password_history` char(64) NOT NULL,
  `id_user_email_account_password_history` int DEFAULT NULL,
  `observation_email_account_password_history` text,
  `date_created_email_account_password_history` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_email_account_password_history`),
  KEY `idx_eah_account` (`id_email_account`),
  KEY `fk_eah_user` (`id_user_email_account_password_history`),
  CONSTRAINT `fk_eah_account` FOREIGN KEY (`id_email_account`) REFERENCES `email_accounts` (`id_email_account`),
  CONSTRAINT `fk_eah_user` FOREIGN KEY (`id_user_email_account_password_history`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: email_accounts
CREATE TABLE `email_accounts` (
  `id_email_account` int NOT NULL AUTO_INCREMENT,
  `name_email_account` varchar(150) NOT NULL,
  `email_email_account` varchar(180) NOT NULL,
  `phone_email_account` varchar(40) DEFAULT NULL,
  `password_email_account` text NOT NULL,
  `description_email_account` text,
  `status_email_account` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `status_deleted_email_account` tinyint NOT NULL DEFAULT '0',
  `id_user_created_email_account` int DEFAULT NULL,
  `id_user_updated_email_account` int DEFAULT NULL,
  `date_created_email_account` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_email_account` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_email_account`),
  UNIQUE KEY `uk_email_accounts_email` (`email_email_account`),
  KEY `idx_email_accounts_status` (`status_deleted_email_account`,`status_email_account`),
  KEY `fk_email_accounts_created_user` (`id_user_created_email_account`),
  KEY `fk_email_accounts_updated_user` (`id_user_updated_email_account`),
  CONSTRAINT `fk_email_accounts_created_user` FOREIGN KEY (`id_user_created_email_account`) REFERENCES `users` (`id_user`),
  CONSTRAINT `fk_email_accounts_updated_user` FOREIGN KEY (`id_user_updated_email_account`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: employees
CREATE TABLE `employees` (
  `id_employee` int NOT NULL AUTO_INCREMENT,
  `id_area_employee` int NOT NULL,
  `id_position_employee` int NOT NULL,
  `id_professional_title_employee` int NOT NULL,
  `first_name_employee` varchar(150) NOT NULL,
  `middle_name_employee` varchar(150) DEFAULT NULL,
  `preferred_name_employee` tinyint NOT NULL DEFAULT '1',
  `paternal_last_name_employee` varchar(150) NOT NULL,
  `maternal_last_name_employee` varchar(150) NOT NULL,
  `dni_employee` varchar(100) NOT NULL,
  `cell_phone_employee` varchar(9) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `email_employee` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status_active_employee` tinyint NOT NULL DEFAULT '1',
  `status_deleted_employee` tinyint NOT NULL DEFAULT '0',
  `date_created_employee` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_employee` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_employee`),
  UNIQUE KEY `email_employee` (`email_employee`),
  KEY `id_area_employee` (`id_area_employee`),
  KEY `id_position_employee` (`id_position_employee`),
  KEY `id_professional_title_employee` (`id_professional_title_employee`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`id_area_employee`) REFERENCES `areas` (`id_area`),
  CONSTRAINT `employees_ibfk_2` FOREIGN KEY (`id_position_employee`) REFERENCES `positions` (`id_position`),
  CONSTRAINT `employees_ibfk_3` FOREIGN KEY (`id_professional_title_employee`) REFERENCES `professional_titles` (`id_professional_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: environments
CREATE TABLE `environments` (
  `id_environment` int NOT NULL AUTO_INCREMENT,
  `id_campus_environment` int NOT NULL,
  `name_environment` varchar(100) NOT NULL,
  `floor_environment` int NOT NULL,
  `use_environment` text,
  `status_active_environment` tinyint NOT NULL DEFAULT '1',
  `status_deleted_environment` tinyint NOT NULL DEFAULT '0',
  `date_created_environment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_environment` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_environment`),
  KEY `id_campus_space` (`id_campus_environment`),
  CONSTRAINT `environments_ibfk_1` FOREIGN KEY (`id_campus_environment`) REFERENCES `campuses` (`id_campus`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: floor_plan_versions
CREATE TABLE `floor_plan_versions` (
  `id_floor_plan_version` int NOT NULL AUTO_INCREMENT,
  `id_floor_plan` int NOT NULL,
  `version_number_floor_plan_version` int NOT NULL,
  `route_file_floor_plan_version` varchar(255) NOT NULL,
  `name_file_floor_plan_version` varchar(255) NOT NULL,
  `route_image_floor_plan_version` varchar(255) DEFAULT NULL,
  `image_width_floor_plan_version` int DEFAULT NULL,
  `image_height_floor_plan_version` int DEFAULT NULL,
  `observation_floor_plan_version` text,
  `id_user_floor_plan_version` int DEFAULT NULL,
  `date_created_floor_plan_version` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_floor_plan_version`),
  KEY `idx_floor_plan_version_plan` (`id_floor_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: floor_plans
CREATE TABLE `floor_plans` (
  `id_floor_plan` int NOT NULL AUTO_INCREMENT,
  `name_floor_plan` varchar(160) NOT NULL,
  `description_floor_plan` text,
  `id_campus_floor_plan` int NOT NULL,
  `floor_floor_plan` int NOT NULL,
  `current_file_floor_plan` varchar(255) DEFAULT NULL,
  `current_file_name_floor_plan` varchar(255) DEFAULT NULL,
  `current_image_floor_plan` varchar(255) DEFAULT NULL,
  `current_image_name_floor_plan` varchar(255) DEFAULT NULL,
  `current_image_width_floor_plan` int DEFAULT NULL,
  `current_image_height_floor_plan` int DEFAULT NULL,
  `version_floor_plan` int NOT NULL DEFAULT '1',
  `status_active_floor_plan` tinyint NOT NULL DEFAULT '1',
  `status_deleted_floor_plan` tinyint NOT NULL DEFAULT '0',
  `id_user_created_floor_plan` int DEFAULT NULL,
  `id_user_updated_floor_plan` int DEFAULT NULL,
  `date_created_floor_plan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_floor_plan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_floor_plan`),
  KEY `idx_floor_plan_campus_floor` (`id_campus_floor_plan`,`floor_floor_plan`),
  KEY `idx_floor_plan_status` (`status_deleted_floor_plan`,`status_active_floor_plan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: frequencies_maintenance
CREATE TABLE `frequencies_maintenance` (
  `id_frequency_maintenance` int NOT NULL AUTO_INCREMENT,
  `name_frequency_maintenance` varchar(100) NOT NULL,
  `interval_frequency_maintenance` int NOT NULL,
  `description_frequency_maintenance` text NOT NULL,
  `status_active_frequency_maintenance` tinyint NOT NULL DEFAULT '1',
  `status_deleted_frequency_maintenance` tinyint NOT NULL DEFAULT '0',
  `date_created_frequency_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_frequency_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_frequency_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: history_maintenances_corrective
CREATE TABLE `history_maintenances_corrective` (
  `id_history_maintenance_corrective` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_corrective` int NOT NULL,
  `id_frequency_maintenance` int NOT NULL,
  `id_status_maintenance` int NOT NULL,
  `id_type_responsible_maintenance` int NOT NULL,
  `date_scheduled_history_maintenance_corrective` date NOT NULL,
  `date_performed_history_maintenance_corrective` date DEFAULT NULL,
  `action_history_maintenance_corrective` text NOT NULL,
  `observation_history_maintenance_corrective` text NOT NULL,
  `date_created_history_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_history_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history_maintenance_corrective`),
  KEY `idx_hmc_maintenance` (`id_maintenance_corrective`),
  KEY `idx_hmc_frequency` (`id_frequency_maintenance`),
  KEY `idx_hmc_status` (`id_status_maintenance`),
  KEY `idx_hmc_responsible` (`id_type_responsible_maintenance`),
  CONSTRAINT `fk_hmc_frequency` FOREIGN KEY (`id_frequency_maintenance`) REFERENCES `frequencies_maintenance` (`id_frequency_maintenance`),
  CONSTRAINT `fk_hmc_maintenance` FOREIGN KEY (`id_maintenance_corrective`) REFERENCES `maintenances_corrective` (`id_maintenance_corrective`),
  CONSTRAINT `fk_hmc_responsible` FOREIGN KEY (`id_type_responsible_maintenance`) REFERENCES `types_responsible_maintenance` (`id_type_responsible_maintenance`),
  CONSTRAINT `fk_hmc_status` FOREIGN KEY (`id_status_maintenance`) REFERENCES `states_maintenance` (`id_status_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: history_maintenances_corrective_company
CREATE TABLE `history_maintenances_corrective_company` (
  `id_history_maintenance_corrective_company` int NOT NULL AUTO_INCREMENT,
  `id_history_maintenance_corrective` int NOT NULL,
  `id_company` int NOT NULL,
  `status_active_history_maintenance_corrective_company` tinyint NOT NULL DEFAULT '1',
  `status_deleted_history_maintenance_corrective_company` tinyint NOT NULL DEFAULT '0',
  `date_created_history_maintenance_corrective_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_history_maintenance_corrective_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history_maintenance_corrective_company`),
  KEY `idx_hmcc_history` (`id_history_maintenance_corrective`),
  KEY `idx_hmcc_company` (`id_company`),
  CONSTRAINT `fk_hmcc_company` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`),
  CONSTRAINT `fk_hmcc_history` FOREIGN KEY (`id_history_maintenance_corrective`) REFERENCES `history_maintenances_corrective` (`id_history_maintenance_corrective`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: history_maintenances_preventive
CREATE TABLE `history_maintenances_preventive` (
  `id_history_maintenance_preventive` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_preventive` int NOT NULL,
  `id_frequency_maintenance` int NOT NULL,
  `id_status_maintenance` int NOT NULL,
  `id_type_responsible_maintenance` int NOT NULL,
  `date_scheduled_history_maintenance_preventive` date NOT NULL,
  `date_performed_history_maintenance_preventive` date DEFAULT NULL,
  `time_performed_history_maintenance_preventive` time DEFAULT NULL,
  `action_history_maintenance_preventive` text NOT NULL,
  `observation_history_maintenance_preventive` text NOT NULL,
  `date_created_history_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_history_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history_maintenance_preventive`),
  KEY `id_maintenance_preventive` (`id_maintenance_preventive`),
  KEY `id_frequency_maintenance` (`id_frequency_maintenance`),
  KEY `id_status_maintenance` (`id_status_maintenance`),
  KEY `id_type_responsible_maintenance` (`id_type_responsible_maintenance`),
  CONSTRAINT `history_maintenances_preventive_ibfk_1` FOREIGN KEY (`id_maintenance_preventive`) REFERENCES `maintenances_preventive` (`id_maintenance_preventive`),
  CONSTRAINT `history_maintenances_preventive_ibfk_2` FOREIGN KEY (`id_frequency_maintenance`) REFERENCES `frequencies_maintenance` (`id_frequency_maintenance`),
  CONSTRAINT `history_maintenances_preventive_ibfk_3` FOREIGN KEY (`id_status_maintenance`) REFERENCES `states_maintenance` (`id_status_maintenance`),
  CONSTRAINT `history_maintenances_preventive_ibfk_4` FOREIGN KEY (`id_type_responsible_maintenance`) REFERENCES `types_responsible_maintenance` (`id_type_responsible_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: history_maintenances_preventive_company
CREATE TABLE `history_maintenances_preventive_company` (
  `id_history_maintenance_preventive_company` int NOT NULL AUTO_INCREMENT,
  `id_history_maintenance_preventive` int NOT NULL,
  `id_company` int NOT NULL,
  `status_active_history_maintenance_preventive_company` tinyint NOT NULL DEFAULT '1',
  `status_deleted_history_maintenance_preventive_company` tinyint NOT NULL DEFAULT '0',
  `date_created_history_maintenance_preventive_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_history_maintenance_preventive_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history_maintenance_preventive_company`),
  KEY `id_history_maintenance_preventive` (`id_history_maintenance_preventive`),
  KEY `id_company` (`id_company`),
  CONSTRAINT `history_maintenances_preventive_company_ibfk_1` FOREIGN KEY (`id_history_maintenance_preventive`) REFERENCES `history_maintenances_preventive` (`id_history_maintenance_preventive`),
  CONSTRAINT `history_maintenances_preventive_company_ibfk_2` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: incident_corrective_derivation_history
CREATE TABLE `incident_corrective_derivation_history` (
  `id_incident_corrective_derivation_history` int NOT NULL AUTO_INCREMENT,
  `id_incident` int NOT NULL,
  `id_maintenance_corrective` int DEFAULT NULL,
  `action_incident_corrective_derivation_history` varchar(30) NOT NULL,
  `observation_incident_corrective_derivation_history` text,
  `id_user_incident_corrective_derivation_history` int DEFAULT NULL,
  `date_created_incident_corrective_derivation_history` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_incident_corrective_derivation_history`),
  KEY `idx_icdh_incident` (`id_incident`),
  KEY `idx_icdh_maintenance` (`id_maintenance_corrective`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: incident_events
CREATE TABLE `incident_events` (
  `id_incident_event` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_incident` int NOT NULL,
  `event_type` varchar(30) NOT NULL,
  `id_user` int DEFAULT NULL,
  `solution_snapshot` text,
  `resolution_date` date DEFAULT NULL,
  `resolution_time` time DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `source_key` varchar(80) DEFAULT NULL,
  PRIMARY KEY (`id_incident_event`),
  UNIQUE KEY `uq_incident_event_source` (`source_key`),
  KEY `idx_incident_events` (`id_incident`,`id_incident_event`),
  CONSTRAINT `fk_incident_events_incident` FOREIGN KEY (`id_incident`) REFERENCES `incidents` (`id_incident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: incidents
CREATE TABLE `incidents` (
  `id_incident` int NOT NULL AUTO_INCREMENT,
  `id_user_incident` int NOT NULL,
  `id_asset_incident` int DEFAULT NULL,
  `corrective_derivation_status_incident` varchar(20) NOT NULL DEFAULT 'none',
  `id_maintenance_corrective_incident` int DEFAULT NULL,
  `date_derived_corrective_incident` timestamp NULL DEFAULT NULL,
  `date_updated_corrective_derivation_incident` timestamp NULL DEFAULT NULL,
  `id_space_incident` int NOT NULL,
  `id_support_assigned_incident` int DEFAULT NULL,
  `id_category_asset_incident` int DEFAULT NULL,
  `id_type_asset_incident` int DEFAULT NULL,
  `id_priority_incident` int NOT NULL,
  `title_incident` text NOT NULL,
  `description_incident` text NOT NULL,
  `status_open_incident` tinyint NOT NULL DEFAULT '1',
  `status_active_incident` tinyint NOT NULL DEFAULT '1',
  `status_deleted_incident` tinyint NOT NULL DEFAULT '0',
  `date_created_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_assigned_incident` timestamp NULL DEFAULT NULL,
  `datetime_closed_record` timestamp NULL DEFAULT NULL,
  `date_closed_incident` date DEFAULT NULL,
  `time_closed_incident` time DEFAULT NULL,
  `diagnostic_incident` text,
  `solution_incident` text,
  `id_type_incident_incident` int DEFAULT NULL,
  `date_incident_date` date DEFAULT NULL,
  `time_incident_time` time DEFAULT NULL,
  PRIMARY KEY (`id_incident`),
  KEY `idx_incident_user` (`id_user_incident`),
  KEY `idx_incident_asset` (`id_asset_incident`),
  KEY `idx_incident_environment` (`id_space_incident`),
  KEY `idx_incident_support_assigned` (`id_support_assigned_incident`),
  KEY `idx_incident_category_asset` (`id_category_asset_incident`),
  KEY `idx_incident_type_asset` (`id_type_asset_incident`),
  KEY `idx_incident_priority` (`id_priority_incident`),
  KEY `idx_incident_type_incident` (`id_type_incident_incident`),
  CONSTRAINT `fk_incidents_type_incident` FOREIGN KEY (`id_type_incident_incident`) REFERENCES `types_incident` (`id_type_incident`),
  CONSTRAINT `incidents_ibfk_1` FOREIGN KEY (`id_user_incident`) REFERENCES `users` (`id_user`),
  CONSTRAINT `incidents_ibfk_2` FOREIGN KEY (`id_asset_incident`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `incidents_ibfk_3` FOREIGN KEY (`id_space_incident`) REFERENCES `environments` (`id_environment`),
  CONSTRAINT `incidents_ibfk_4` FOREIGN KEY (`id_support_assigned_incident`) REFERENCES `users` (`id_user`),
  CONSTRAINT `incidents_ibfk_5` FOREIGN KEY (`id_category_asset_incident`) REFERENCES `categories_asset` (`id_category_asset`),
  CONSTRAINT `incidents_ibfk_6` FOREIGN KEY (`id_type_asset_incident`) REFERENCES `types_asset` (`id_type_asset`),
  CONSTRAINT `incidents_ibfk_7` FOREIGN KEY (`id_priority_incident`) REFERENCES `priorities` (`id_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: label_templates
CREATE TABLE `label_templates` (
  `id_label_template` int NOT NULL AUTO_INCREMENT,
  `name_label_template` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `width_cm_label_template` decimal(8,2) NOT NULL,
  `height_cm_label_template` decimal(8,2) NOT NULL,
  `description_label_template` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status_active_label_template` tinyint(1) NOT NULL DEFAULT '1',
  `status_deleted_label_template` tinyint(1) NOT NULL DEFAULT '0',
  `design_route_label_template` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qr_x_percent_label_template` decimal(8,2) NOT NULL DEFAULT '60.00',
  `qr_y_percent_label_template` decimal(8,2) NOT NULL DEFAULT '20.00',
  `qr_width_percent_label_template` decimal(8,2) NOT NULL DEFAULT '30.00',
  `qr_height_percent_label_template` decimal(8,2) NOT NULL DEFAULT '45.00',
  `code_x_percent_label_template` decimal(8,2) NOT NULL DEFAULT '5.00',
  `code_y_percent_label_template` decimal(8,2) NOT NULL DEFAULT '75.00',
  `code_width_percent_label_template` decimal(8,2) NOT NULL DEFAULT '45.00',
  `code_height_percent_label_template` decimal(8,2) NOT NULL DEFAULT '12.00',
  `code_font_size_label_template` decimal(8,2) NOT NULL DEFAULT '8.00',
  `date_created_label_template` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_label_template` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_label_template`),
  KEY `idx_label_templates_status` (`status_active_label_template`),
  KEY `idx_label_templates_deleted` (`status_deleted_label_template`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: login_attempt_limits
CREATE TABLE `login_attempt_limits` (
  `attempt_key` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `attempts` smallint unsigned NOT NULL DEFAULT '0',
  `window_started` datetime NOT NULL,
  PRIMARY KEY (`attempt_key`),
  KEY `idx_login_limit_expiration` (`window_started`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenance_corrective_origins
CREATE TABLE `maintenance_corrective_origins` (
  `id_maintenance_corrective_origin` int NOT NULL AUTO_INCREMENT,
  `name_maintenance_corrective_origin` varchar(120) NOT NULL,
  `description_maintenance_corrective_origin` text,
  `status_active_maintenance_corrective_origin` tinyint NOT NULL DEFAULT '1',
  `status_deleted_maintenance_corrective_origin` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_corrective_origin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_corrective_origin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_corrective_origin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenance_corrective_spare_parts
CREATE TABLE `maintenance_corrective_spare_parts` (
  `id_maintenance_corrective_spare_part` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_corrective` int NOT NULL,
  `id_spare_part` int NOT NULL,
  `quantity_maintenance_corrective_spare_part` decimal(10,2) NOT NULL DEFAULT '1.00',
  `observation_maintenance_corrective_spare_part` text,
  `status_deleted_maintenance_corrective_spare_part` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_corrective_spare_part` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_corrective_spare_part` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_corrective_spare_part`),
  KEY `idx_mcsp_maintenance` (`id_maintenance_corrective`),
  KEY `idx_mcsp_spare_part` (`id_spare_part`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenances_corrective
CREATE TABLE `maintenances_corrective` (
  `id_maintenance_corrective` int NOT NULL AUTO_INCREMENT,
  `id_asset_maintenance_corrective` int NOT NULL,
  `id_incident_maintenance_corrective` int DEFAULT NULL,
  `id_origin_maintenance_corrective` int DEFAULT NULL,
  `id_frequency_maintenance_maintenance_corrective` int NOT NULL,
  `id_type_responsible_maintenance_maintenance_corrective` int NOT NULL,
  `date_scheduled_maintenance_corrective` date DEFAULT NULL,
  `date_next_maintenance_corrective` date DEFAULT NULL,
  `date_repair_maintenance_corrective` date DEFAULT NULL,
  `time_repair_maintenance_corrective` time DEFAULT NULL,
  `action_maintenance_corrective` text,
  `description_maintenance_corrective` text NOT NULL,
  `observation_maintenance_corrective` text NOT NULL,
  `status_active_maintenance_corrective` tinyint NOT NULL DEFAULT '1',
  `status_deleted_maintenance_corrective` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_corrective` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_corrective`),
  KEY `idx_mc_asset` (`id_asset_maintenance_corrective`),
  KEY `idx_mc_incident` (`id_incident_maintenance_corrective`),
  KEY `idx_mc_frequency` (`id_frequency_maintenance_maintenance_corrective`),
  KEY `idx_mc_responsible` (`id_type_responsible_maintenance_maintenance_corrective`),
  CONSTRAINT `fk_mc_asset` FOREIGN KEY (`id_asset_maintenance_corrective`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `fk_mc_frequency` FOREIGN KEY (`id_frequency_maintenance_maintenance_corrective`) REFERENCES `frequencies_maintenance` (`id_frequency_maintenance`),
  CONSTRAINT `fk_mc_incident` FOREIGN KEY (`id_incident_maintenance_corrective`) REFERENCES `incidents` (`id_incident`),
  CONSTRAINT `fk_mc_responsible` FOREIGN KEY (`id_type_responsible_maintenance_maintenance_corrective`) REFERENCES `types_responsible_maintenance` (`id_type_responsible_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenances_corrective_company
CREATE TABLE `maintenances_corrective_company` (
  `id_maintenance_corrective_company` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_corrective` int NOT NULL,
  `id_company` int NOT NULL,
  `status_active_maintenance_corrective_company` tinyint NOT NULL DEFAULT '1',
  `status_deleted_maintenance_corrective_company` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_corrective_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_corrective_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_corrective_company`),
  KEY `idx_mcc_maintenance` (`id_maintenance_corrective`),
  KEY `idx_mcc_company` (`id_company`),
  CONSTRAINT `fk_mcc_company` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`),
  CONSTRAINT `fk_mcc_maintenance` FOREIGN KEY (`id_maintenance_corrective`) REFERENCES `maintenances_corrective` (`id_maintenance_corrective`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenances_preventive
CREATE TABLE `maintenances_preventive` (
  `id_maintenance_preventive` int NOT NULL AUTO_INCREMENT,
  `id_asset_maintenance_preventive` int NOT NULL,
  `id_frequency_maintenance_maintenance_preventive` int NOT NULL,
  `id_type_responsible_maintenance_maintenance_preventive` int NOT NULL,
  `date_scheduled_maintenance_preventive` date DEFAULT NULL,
  `date_next_maintenance_preventive` date DEFAULT NULL,
  `description_maintenance_preventive` text NOT NULL,
  `observation_maintenance_preventive` text NOT NULL,
  `status_active_maintenance_preventive` tinyint NOT NULL DEFAULT '1',
  `status_deleted_maintenance_preventive` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_preventive` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_preventive`),
  UNIQUE KEY `id_asset_maintenance_preventive` (`id_asset_maintenance_preventive`),
  KEY `id_frequency_maintenance_maintenance_preventive` (`id_frequency_maintenance_maintenance_preventive`),
  KEY `id_type_responsible_maintenance_maintenance_preventive` (`id_type_responsible_maintenance_maintenance_preventive`),
  CONSTRAINT `maintenances_preventive_ibfk_1` FOREIGN KEY (`id_asset_maintenance_preventive`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `maintenances_preventive_ibfk_2` FOREIGN KEY (`id_frequency_maintenance_maintenance_preventive`) REFERENCES `frequencies_maintenance` (`id_frequency_maintenance`),
  CONSTRAINT `maintenances_preventive_ibfk_3` FOREIGN KEY (`id_type_responsible_maintenance_maintenance_preventive`) REFERENCES `types_responsible_maintenance` (`id_type_responsible_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: maintenances_preventive_company
CREATE TABLE `maintenances_preventive_company` (
  `id_maintenance_preventive_company` int NOT NULL AUTO_INCREMENT,
  `id_maintenance_preventive` int NOT NULL,
  `id_company` int NOT NULL,
  `status_active_maintenance_preventive_company` tinyint NOT NULL DEFAULT '1',
  `status_deleted_maintenance_preventive_company` tinyint NOT NULL DEFAULT '0',
  `date_created_maintenance_preventive_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_maintenance_preventive_company` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_maintenance_preventive_company`),
  KEY `id_maintenance_preventive` (`id_maintenance_preventive`),
  KEY `id_company` (`id_company`),
  CONSTRAINT `maintenances_preventive_company_ibfk_1` FOREIGN KEY (`id_maintenance_preventive`) REFERENCES `maintenances_preventive` (`id_maintenance_preventive`),
  CONSTRAINT `maintenances_preventive_company_ibfk_2` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: modules
CREATE TABLE `modules` (
  `id_module` int NOT NULL AUTO_INCREMENT,
  `name_module` varchar(150) NOT NULL,
  `description_module` text NOT NULL,
  `status_active_module` tinyint NOT NULL DEFAULT '1',
  `status_deleted_module` tinyint NOT NULL DEFAULT '0',
  `date_created_module` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_module` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: movement_changes
CREATE TABLE `movement_changes` (
  `id_movement_change` int NOT NULL AUTO_INCREMENT,
  `id_movement_asset` int NOT NULL,
  `id_user` int NOT NULL,
  `action_change` varchar(20) NOT NULL,
  `reason_change` text NOT NULL,
  `previous_data` json NOT NULL,
  `new_data` json NOT NULL,
  `date_created_change` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_movement_change`),
  KEY `idx_movement_changes` (`id_movement_asset`,`date_created_change`),
  CONSTRAINT `fk_movement_change_record` FOREIGN KEY (`id_movement_asset`) REFERENCES `movements_asset` (`id_movement_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: movements_asset
CREATE TABLE `movements_asset` (
  `id_movement_asset` int NOT NULL AUTO_INCREMENT,
  `id_asset_movement_asset` int NOT NULL,
  `id_space_movement_asset` int NOT NULL,
  `id_floor_plan_movement_asset` int DEFAULT NULL,
  `coordinate_x_movement_asset` decimal(6,2) DEFAULT NULL,
  `coordinate_y_movement_asset` decimal(6,2) DEFAULT NULL,
  `id_type_movement_asset` int NOT NULL,
  `id_responsible_employee_movement_asset` int DEFAULT NULL,
  `manual_responsible_movement_asset` varchar(160) DEFAULT NULL,
  `observations_movement_asset` text NOT NULL,
  `status_record_current_movement_asset` tinyint NOT NULL DEFAULT '1',
  `status_active_movement_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_movement_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_movement_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_movement_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_event_movement_asset` date DEFAULT NULL,
  `time_event_movement_asset` time DEFAULT NULL,
  `expected_return_date_movement_asset` date DEFAULT NULL,
  `document_code_movement_asset` varchar(80) DEFAULT NULL,
  `status_process_movement_asset` varchar(40) NOT NULL DEFAULT 'Registrado',
  `loan_recipient_type_movement_asset` varchar(30) DEFAULT NULL,
  `loan_recipient_name_movement_asset` varchar(160) DEFAULT NULL,
  `loan_recipient_document_movement_asset` varchar(80) DEFAULT NULL,
  `loan_return_indefinite_movement_asset` tinyint(1) NOT NULL DEFAULT '0',
  `status_annulled_movement_asset` tinyint NOT NULL DEFAULT '0',
  `current_asset_id` int GENERATED ALWAYS AS ((case when ((`status_record_current_movement_asset` = 1) and (`status_deleted_movement_asset` = 0) and (`status_annulled_movement_asset` = 0)) then `id_asset_movement_asset` else NULL end)) STORED,
  PRIMARY KEY (`id_movement_asset`),
  UNIQUE KEY `uq_movement_current_asset` (`current_asset_id`),
  KEY `id_asset_movement_asset` (`id_asset_movement_asset`),
  KEY `id_space_movement_asset` (`id_space_movement_asset`),
  KEY `id_type_movement_asset` (`id_type_movement_asset`),
  KEY `idx_movements_asset_responsible_employee` (`id_responsible_employee_movement_asset`),
  KEY `idx_movements_asset_loan_type` (`loan_recipient_type_movement_asset`),
  CONSTRAINT `movements_asset_ibfk_1` FOREIGN KEY (`id_asset_movement_asset`) REFERENCES `assets` (`id_asset`),
  CONSTRAINT `movements_asset_ibfk_2` FOREIGN KEY (`id_space_movement_asset`) REFERENCES `environments` (`id_environment`),
  CONSTRAINT `movements_asset_ibfk_3` FOREIGN KEY (`id_type_movement_asset`) REFERENCES `types_movement` (`id_type_movement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: network_segmentation_ips
CREATE TABLE `network_segmentation_ips` (
  `id_network_segmentation_ip` int NOT NULL AUTO_INCREMENT,
  `id_network_segmentation_network_segmentation_ip` int NOT NULL,
  `ip_address_network_segmentation_ip` varchar(45) NOT NULL,
  `status_network_segmentation_ip` enum('available','assigned','reserved','inactive') NOT NULL DEFAULT 'available',
  `status_deleted_network_segmentation_ip` tinyint NOT NULL DEFAULT '0',
  `date_created_network_segmentation_ip` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_network_segmentation_ip` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_network_segmentation_ip`),
  UNIQUE KEY `uniq_network_segmentation_ip` (`id_network_segmentation_network_segmentation_ip`,`ip_address_network_segmentation_ip`),
  KEY `idx_network_segmentation_ip_status` (`status_network_segmentation_ip`),
  CONSTRAINT `fk_network_segmentation_ips_segment` FOREIGN KEY (`id_network_segmentation_network_segmentation_ip`) REFERENCES `network_segmentations` (`id_network_segmentation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: network_segmentations
CREATE TABLE `network_segmentations` (
  `id_network_segmentation` int NOT NULL AUTO_INCREMENT,
  `name_network_segmentation` varchar(150) NOT NULL,
  `description_network_segmentation` text,
  `network_address_network_segmentation` varchar(45) NOT NULL,
  `subnet_mask_network_segmentation` varchar(45) NOT NULL,
  `gateway_network_segmentation` varchar(45) DEFAULT NULL,
  `dns_network_segmentation` varchar(120) DEFAULT NULL,
  `ip_range_start_network_segmentation` varchar(45) NOT NULL,
  `ip_range_end_network_segmentation` varchar(45) NOT NULL,
  `status_active_network_segmentation` tinyint NOT NULL DEFAULT '1',
  `status_deleted_network_segmentation` tinyint NOT NULL DEFAULT '0',
  `date_created_network_segmentation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_network_segmentation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_network_segmentation`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: os_user_account_password_history
CREATE TABLE `os_user_account_password_history` (
  `id_os_user_account_password_history` int NOT NULL AUTO_INCREMENT,
  `id_os_user_account` int NOT NULL,
  `password_fingerprint_os_user_account_password_history` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_user_os_user_account_password_history` int DEFAULT NULL,
  `observation_os_user_account_password_history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `date_created_os_user_account_password_history` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_os_user_account_password_history`),
  KEY `idx_os_user_account_password_history_account` (`id_os_user_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: os_user_accounts
CREATE TABLE `os_user_accounts` (
  `id_os_user_account` int NOT NULL AUTO_INCREMENT,
  `name_os_user_account` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username_os_user_account` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_os_user_account` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_os_user_account` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `password_updated_at_os_user_account` datetime DEFAULT NULL,
  `status_active_os_user_account` tinyint(1) NOT NULL DEFAULT '1',
  `status_deleted_os_user_account` tinyint(1) NOT NULL DEFAULT '0',
  `id_user_created_os_user_account` int DEFAULT NULL,
  `id_user_updated_os_user_account` int DEFAULT NULL,
  `date_created_os_user_account` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_os_user_account` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_os_user_account`),
  KEY `idx_os_user_accounts_status` (`status_active_os_user_account`),
  KEY `idx_os_user_accounts_deleted` (`status_deleted_os_user_account`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: parameters_maintenance_preventive_actions
CREATE TABLE `parameters_maintenance_preventive_actions` (
  `id_parameter_maintenance_preventive_action` int NOT NULL AUTO_INCREMENT,
  `name_parameter_maintenance_preventive_action` varchar(255) NOT NULL,
  `status_active_parameter_maintenance_preventive_action` tinyint NOT NULL DEFAULT '1',
  `status_deleted_parameter_maintenance_preventive_action` tinyint NOT NULL DEFAULT '0',
  `date_created_parameter_maintenance_preventive_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_parameter_maintenance_preventive_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_parameter_maintenance_preventive_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: permissions
CREATE TABLE `permissions` (
  `id_permission` int NOT NULL AUTO_INCREMENT,
  `id_role_permission` int NOT NULL,
  `id_module_permission` int NOT NULL,
  `r_read_permission` tinyint NOT NULL DEFAULT '0',
  `w_write_permission` tinyint NOT NULL DEFAULT '0',
  `u_update_permission` tinyint NOT NULL DEFAULT '0',
  `d_delete_permission` tinyint NOT NULL DEFAULT '0',
  `date_created_permission` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_permission` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_permission`),
  UNIQUE KEY `uq_permissions_role_module` (`id_role_permission`,`id_module_permission`),
  KEY `id_role_permission` (`id_role_permission`),
  KEY `id_module_permission` (`id_module_permission`),
  CONSTRAINT `permissions_ibfk_1` FOREIGN KEY (`id_role_permission`) REFERENCES `roles` (`id_role`),
  CONSTRAINT `permissions_ibfk_2` FOREIGN KEY (`id_module_permission`) REFERENCES `modules` (`id_module`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: positions
CREATE TABLE `positions` (
  `id_position` int NOT NULL AUTO_INCREMENT,
  `name_position` varchar(100) NOT NULL,
  `status_active_position` tinyint NOT NULL DEFAULT '1',
  `status_deleted_position` tinyint NOT NULL DEFAULT '0',
  `date_created_position` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_position` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: priorities
CREATE TABLE `priorities` (
  `id_priority` int NOT NULL AUTO_INCREMENT,
  `name_priority` varchar(100) NOT NULL,
  `status_active_priority` tinyint NOT NULL DEFAULT '1',
  `status_deleted_priority` tinyint NOT NULL DEFAULT '0',
  `date_created_priority` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_priority` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_priority`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: professional_titles
CREATE TABLE `professional_titles` (
  `id_professional_title` int NOT NULL AUTO_INCREMENT,
  `name_professional_title` varchar(100) NOT NULL,
  `abbreviation_professional_title` varchar(100) NOT NULL,
  `status_active_professional_title` tinyint NOT NULL DEFAULT '1',
  `status_deleted_professional_title` tinyint NOT NULL DEFAULT '0',
  `date_created_professional_title` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_professional_title` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_professional_title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: remote_access_applications
CREATE TABLE `remote_access_applications` (
  `id_remote_access_application` int NOT NULL AUTO_INCREMENT,
  `name_remote_access_application` varchar(80) NOT NULL,
  `description_remote_access_application` text,
  `status_active_remote_access_application` tinyint NOT NULL DEFAULT '1',
  `status_deleted_remote_access_application` tinyint NOT NULL DEFAULT '0',
  `date_created_remote_access_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_remote_access_application` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_remote_access_application`),
  UNIQUE KEY `uq_remote_access_app_name` (`name_remote_access_application`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: remote_access_details
CREATE TABLE `remote_access_details` (
  `id_remote_access_detail` int NOT NULL AUTO_INCREMENT,
  `id_remote_access` int NOT NULL,
  `id_remote_access_application_remote_access_detail` int NOT NULL,
  `access_code_remote_access_detail` varchar(150) NOT NULL,
  `description_remote_access_detail` text,
  `observations_remote_access_detail` text,
  `status_active_remote_access_detail` tinyint NOT NULL DEFAULT '1',
  `status_deleted_remote_access_detail` tinyint NOT NULL DEFAULT '0',
  `id_user_created_remote_access_detail` int DEFAULT NULL,
  `id_user_updated_remote_access_detail` int DEFAULT NULL,
  `date_created_remote_access_detail` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_remote_access_detail` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_remote_access_detail`),
  KEY `idx_remote_access_detail_header` (`id_remote_access`),
  KEY `idx_remote_access_detail_app` (`id_remote_access_application_remote_access_detail`),
  CONSTRAINT `fk_remote_access_detail_app` FOREIGN KEY (`id_remote_access_application_remote_access_detail`) REFERENCES `remote_access_applications` (`id_remote_access_application`),
  CONSTRAINT `fk_remote_access_detail_header` FOREIGN KEY (`id_remote_access`) REFERENCES `remote_accesses` (`id_remote_access`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: remote_access_history
CREATE TABLE `remote_access_history` (
  `id_remote_access_history` int NOT NULL AUTO_INCREMENT,
  `id_remote_access` int NOT NULL,
  `previous_application_remote_access_history` varchar(80) DEFAULT NULL,
  `new_application_remote_access_history` varchar(80) DEFAULT NULL,
  `previous_access_code_remote_access_history` varchar(150) DEFAULT NULL,
  `new_access_code_remote_access_history` varchar(150) DEFAULT NULL,
  `action_remote_access_history` varchar(40) NOT NULL,
  `detail_remote_access_history` text,
  `id_user_remote_access_history` int DEFAULT NULL,
  `date_created_remote_access_history` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_remote_access_history`),
  KEY `idx_remote_access_history` (`id_remote_access`),
  CONSTRAINT `fk_remote_access_history` FOREIGN KEY (`id_remote_access`) REFERENCES `remote_accesses` (`id_remote_access`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: remote_accesses
CREATE TABLE `remote_accesses` (
  `id_remote_access` int NOT NULL AUTO_INCREMENT,
  `id_asset_remote_access` int NOT NULL,
  `id_remote_access_application_remote_access` int DEFAULT NULL,
  `access_code_remote_access` varchar(150) DEFAULT NULL,
  `description_remote_access` text,
  `status_active_remote_access` tinyint NOT NULL DEFAULT '1',
  `status_deleted_remote_access` tinyint NOT NULL DEFAULT '0',
  `id_user_created_remote_access` int DEFAULT NULL,
  `id_user_updated_remote_access` int DEFAULT NULL,
  `date_created_remote_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_remote_access` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_remote_access`),
  UNIQUE KEY `ux_remote_access_asset` (`id_asset_remote_access`),
  KEY `idx_remote_access_asset` (`id_asset_remote_access`),
  KEY `idx_remote_access_app` (`id_remote_access_application_remote_access`),
  CONSTRAINT `fk_remote_access_app` FOREIGN KEY (`id_remote_access_application_remote_access`) REFERENCES `remote_access_applications` (`id_remote_access_application`),
  CONSTRAINT `fk_remote_access_asset` FOREIGN KEY (`id_asset_remote_access`) REFERENCES `assets` (`id_asset`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: roles
CREATE TABLE `roles` (
  `id_role` int NOT NULL AUTO_INCREMENT,
  `name_role` varchar(150) NOT NULL,
  `description_role` text NOT NULL,
  `status_active_role` tinyint NOT NULL DEFAULT '0',
  `status_deleted_role` tinyint NOT NULL DEFAULT '0',
  `date_created_role` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_role` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: softwares
CREATE TABLE `softwares` (
  `id_software` int NOT NULL AUTO_INCREMENT,
  `id_type_license_software` int NOT NULL,
  `id_type_software_software` int NOT NULL,
  `id_architecture_software` int NOT NULL,
  `id_asset_software` int NOT NULL,
  `name_software` varchar(200) NOT NULL,
  `version_software` varchar(100) DEFAULT NULL,
  `description_software` text,
  `status_active_software` tinyint NOT NULL DEFAULT '1',
  `status_deleted_software` tinyint NOT NULL DEFAULT '0',
  `date_created_software` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_software` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_software`),
  KEY `id_type_license_software` (`id_type_license_software`),
  KEY `id_type_software_software` (`id_type_software_software`),
  KEY `id_architecture_software` (`id_architecture_software`),
  KEY `id_asset_software` (`id_asset_software`),
  CONSTRAINT `softwares_ibfk_1` FOREIGN KEY (`id_type_license_software`) REFERENCES `types_license` (`id_type_license`),
  CONSTRAINT `softwares_ibfk_2` FOREIGN KEY (`id_type_software_software`) REFERENCES `types_software` (`id_type_software`),
  CONSTRAINT `softwares_ibfk_3` FOREIGN KEY (`id_architecture_software`) REFERENCES `architectures` (`id_architecture`),
  CONSTRAINT `softwares_ibfk_4` FOREIGN KEY (`id_asset_software`) REFERENCES `assets` (`id_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: spare_part_movements
CREATE TABLE `spare_part_movements` (
  `id_spare_part_movement` int NOT NULL AUTO_INCREMENT,
  `id_spare_part` int NOT NULL,
  `id_maintenance_corrective` int DEFAULT NULL,
  `type_spare_part_movement` enum('IN','OUT','ADJUSTMENT') NOT NULL,
  `quantity_spare_part_movement` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reason_spare_part_movement` text,
  `stock_after_spare_part_movement` decimal(10,2) NOT NULL DEFAULT '0.00',
  `date_created_spare_part_movement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_spare_part_movement`),
  KEY `idx_spm_spare_part` (`id_spare_part`),
  KEY `idx_spm_maintenance` (`id_maintenance_corrective`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: spare_parts
CREATE TABLE `spare_parts` (
  `id_spare_part` int NOT NULL AUTO_INCREMENT,
  `code_spare_part` varchar(60) NOT NULL,
  `name_spare_part` varchar(150) NOT NULL,
  `id_brand_spare_part` int DEFAULT NULL,
  `model_spare_part` varchar(100) DEFAULT NULL,
  `unit_spare_part` varchar(30) NOT NULL DEFAULT 'unidad',
  `stock_current_spare_part` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimum_spare_part` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description_spare_part` text,
  `status_active_spare_part` tinyint NOT NULL DEFAULT '1',
  `status_deleted_spare_part` tinyint NOT NULL DEFAULT '0',
  `date_created_spare_part` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_spare_part` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_spare_part`),
  UNIQUE KEY `uk_spare_parts_code` (`code_spare_part`),
  KEY `idx_spare_parts_brand` (`id_brand_spare_part`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: states_maintenance
CREATE TABLE `states_maintenance` (
  `id_status_maintenance` int NOT NULL AUTO_INCREMENT,
  `name_status_maintenance` varchar(100) NOT NULL,
  `status_active_status_maintenance` tinyint NOT NULL DEFAULT '1',
  `status_deleted_status_maintenance` tinyint NOT NULL DEFAULT '0',
  `date_created_status_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_status_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_status_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: system_settings
CREATE TABLE `system_settings` (
  `setting_key` varchar(120) NOT NULL,
  `setting_value` text,
  `setting_group` varchar(80) NOT NULL DEFAULT 'general',
  `date_created_system_setting` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_system_setting` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_asset
CREATE TABLE `types_asset` (
  `id_type_asset` int NOT NULL AUTO_INCREMENT,
  `id_category_asset_type_asset` int NOT NULL,
  `name_type_asset` varchar(100) NOT NULL,
  `status_active_type_asset` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_asset` tinyint NOT NULL DEFAULT '0',
  `date_created_type_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_asset` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_asset`),
  KEY `id_category_asset_type_asset` (`id_category_asset_type_asset`),
  CONSTRAINT `types_asset_ibfk_1` FOREIGN KEY (`id_category_asset_type_asset`) REFERENCES `categories_asset` (`id_category_asset`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_firmware
CREATE TABLE `types_firmware` (
  `id_type_firmware` int NOT NULL AUTO_INCREMENT,
  `name_type_firmware` varchar(100) NOT NULL,
  `status_active_type_firmware` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_firmware` tinyint NOT NULL DEFAULT '0',
  `date_created_type_firmware` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_firmware` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_firmware`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_incident
CREATE TABLE `types_incident` (
  `id_type_incident` int NOT NULL AUTO_INCREMENT,
  `name_type_incident` varchar(100) NOT NULL,
  `status_active_type_incident` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_incident` tinyint NOT NULL DEFAULT '0',
  `date_created_type_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_incident` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_incident`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_license
CREATE TABLE `types_license` (
  `id_type_license` int NOT NULL AUTO_INCREMENT,
  `name_type_license` varchar(200) NOT NULL,
  `status_active_type_license` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_license` tinyint NOT NULL DEFAULT '0',
  `date_created_type_license` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_license` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_license`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_movement
CREATE TABLE `types_movement` (
  `id_type_movement` int NOT NULL AUTO_INCREMENT,
  `name_type_movement` varchar(100) NOT NULL,
  `status_active_type_movement` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_movement` tinyint NOT NULL DEFAULT '0',
  `date_created_type_movement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_movement` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_movement`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_network_interface
CREATE TABLE `types_network_interface` (
  `id_type_network_interface` int NOT NULL AUTO_INCREMENT,
  `name_type_network_interface` varchar(100) NOT NULL,
  `status_active_type_network_interface` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_network_interface` tinyint NOT NULL DEFAULT '0',
  `date_created_type_network_interface` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_network_interface` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_network_interface`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_responsible_maintenance
CREATE TABLE `types_responsible_maintenance` (
  `id_type_responsible_maintenance` int NOT NULL AUTO_INCREMENT,
  `name_type_responsible_maintenance` varchar(100) NOT NULL,
  `status_active_type_responsible_maintenance` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_responsible_maintenance` tinyint NOT NULL DEFAULT '0',
  `date_created_type_responsible_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_responsible_maintenance` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_responsible_maintenance`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: types_software
CREATE TABLE `types_software` (
  `id_type_software` int NOT NULL AUTO_INCREMENT,
  `name_type_software` varchar(200) NOT NULL,
  `status_active_type_software` tinyint NOT NULL DEFAULT '1',
  `status_deleted_type_software` tinyint NOT NULL DEFAULT '0',
  `date_created_type_software` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_type_software` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_type_software`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: user_browser_sessions
CREATE TABLE `user_browser_sessions` (
  `id_user_browser_session` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `status_active_user_browser_session` tinyint NOT NULL DEFAULT '1',
  `status_closed_user_browser_session` tinyint NOT NULL DEFAULT '0',
  `date_last_activity_user_browser_session` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created_user_browser_session` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_user_browser_session` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_user_browser_session`),
  UNIQUE KEY `uk_user_browser_session_session_id` (`session_id`),
  KEY `idx_user_browser_sessions_user` (`id_user`),
  KEY `idx_user_browser_sessions_activity` (`date_last_activity_user_browser_session`),
  CONSTRAINT `fk_user_browser_sessions_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: users
CREATE TABLE `users` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `id_role_user` int NOT NULL,
  `id_employee_user` int NOT NULL,
  `name_user` varchar(200) NOT NULL,
  `password_user` varchar(200) NOT NULL,
  `token_permission_user` text,
  `token_expiration_user` text,
  `theme_preference_user` varchar(10) NOT NULL DEFAULT 'light',
  `profile_photo_user` varchar(255) DEFAULT NULL,
  `status_active_user` tinyint NOT NULL DEFAULT '1',
  `status_deleted_user` tinyint NOT NULL DEFAULT '0',
  `date_created_user` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_user` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `protected_super_user` tinyint GENERATED ALWAYS AS ((case when (`id_role_user` = 1) then 1 else NULL end)) STORED,
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `name_user` (`name_user`),
  UNIQUE KEY `uq_users_one_super` (`protected_super_user`),
  KEY `id_role_user` (`id_role_user`),
  KEY `id_employee_user` (`id_employee_user`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_role_user`) REFERENCES `roles` (`id_role`),
  CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_employee_user`) REFERENCES `employees` (`id_employee`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: versions_ip
CREATE TABLE `versions_ip` (
  `id_version_ip` int NOT NULL AUTO_INCREMENT,
  `name_version_ip` varchar(100) NOT NULL,
  `status_active_version_ip` tinyint NOT NULL DEFAULT '1',
  `status_deleted_version_ip` tinyint NOT NULL DEFAULT '0',
  `date_created_version_ip` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_version_ip` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_version_ip`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: wifi_network_history
CREATE TABLE `wifi_network_history` (
  `id_wifi_network_history` int NOT NULL AUTO_INCREMENT,
  `id_wifi_network` int NOT NULL,
  `previous_ssid_wifi_network_history` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_ssid_wifi_network_history` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_fingerprint_wifi_network_history` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_wifi_network_history` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `detail_wifi_network_history` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `id_user_wifi_network_history` int DEFAULT NULL,
  `date_created_wifi_network_history` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wifi_network_history`),
  KEY `idx_wifi_network_history_network` (`id_wifi_network`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: wifi_networks
CREATE TABLE `wifi_networks` (
  `id_wifi_network` int NOT NULL AUTO_INCREMENT,
  `id_asset_wifi_network` int NOT NULL,
  `management_ip_wifi_network` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ssid_wifi_network` varchar(180) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_wifi_network` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_wifi_network` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status_active_wifi_network` tinyint(1) NOT NULL DEFAULT '1',
  `status_deleted_wifi_network` tinyint(1) NOT NULL DEFAULT '0',
  `id_user_created_wifi_network` int DEFAULT NULL,
  `id_user_updated_wifi_network` int DEFAULT NULL,
  `date_created_wifi_network` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_wifi_network` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_wifi_network`),
  KEY `idx_wifi_network_asset` (`id_asset_wifi_network`),
  KEY `idx_wifi_network_status` (`status_active_wifi_network`),
  KEY `idx_wifi_network_deleted` (`status_deleted_wifi_network`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla: windows_credential_password_history
CREATE TABLE `windows_credential_password_history` (
  `id_windows_credential_password_history` int NOT NULL AUTO_INCREMENT,
  `id_windows_credential` int NOT NULL,
  `password_fingerprint_windows_credential_password_history` char(64) NOT NULL,
  `id_user_windows_credential_password_history` int DEFAULT NULL,
  `observation_windows_credential_password_history` text,
  `date_created_windows_credential_password_history` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_windows_credential_password_history`),
  KEY `idx_wch_credential` (`id_windows_credential`),
  KEY `fk_wch_user` (`id_user_windows_credential_password_history`),
  CONSTRAINT `fk_wch_credential` FOREIGN KEY (`id_windows_credential`) REFERENCES `windows_credentials` (`id_windows_credential`),
  CONSTRAINT `fk_wch_user` FOREIGN KEY (`id_user_windows_credential_password_history`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla: windows_credentials
CREATE TABLE `windows_credentials` (
  `id_windows_credential` int NOT NULL AUTO_INCREMENT,
  `name_windows_credential` varchar(150) NOT NULL,
  `network_address_windows_credential` varchar(255) NOT NULL,
  `username_windows_credential` varchar(150) NOT NULL,
  `password_windows_credential` text NOT NULL,
  `description_windows_credential` text,
  `password_updated_at_windows_credential` datetime DEFAULT NULL,
  `status_active_windows_credential` tinyint NOT NULL DEFAULT '1',
  `status_deleted_windows_credential` tinyint NOT NULL DEFAULT '0',
  `id_user_created_windows_credential` int DEFAULT NULL,
  `id_user_updated_windows_credential` int DEFAULT NULL,
  `date_created_windows_credential` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated_windows_credential` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_windows_credential`),
  KEY `idx_windows_credentials_status` (`status_deleted_windows_credential`,`status_active_windows_credential`),
  KEY `idx_windows_credentials_created_user` (`id_user_created_windows_credential`),
  KEY `idx_windows_credentials_updated_user` (`id_user_updated_windows_credential`),
  CONSTRAINT `fk_windows_credentials_created_user` FOREIGN KEY (`id_user_created_windows_credential`) REFERENCES `users` (`id_user`),
  CONSTRAINT `fk_windows_credentials_updated_user` FOREIGN KEY (`id_user_updated_windows_credential`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS=1;

-- Protecciones del único Super Administrador.
DELIMITER $$
CREATE TRIGGER `trg_roles_protect_super_before_update` BEFORE UPDATE ON `roles` FOR EACH ROW BEGIN IF OLD.id_role=1 AND (NEW.id_role<>1 OR NEW.name_role<>OLD.name_role OR NEW.status_active_role<>1 OR NEW.status_deleted_role<>0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El rol Super Administrador estÃ¡ protegido.'; END IF; END$$

CREATE TRIGGER `trg_roles_protect_super_before_delete` BEFORE DELETE ON `roles` FOR EACH ROW BEGIN IF OLD.id_role=1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El rol Super Administrador no puede eliminarse.'; END IF; END$$

CREATE TRIGGER `trg_users_protect_super_before_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN IF NEW.id_role_user=1 AND (NEW.status_active_user<>1 OR NEW.status_deleted_user<>0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El Super Administrador debe estar activo.'; END IF; END$$

CREATE TRIGGER `trg_users_protect_super_before_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN IF OLD.id_role_user=1 AND (NEW.id_role_user<>1 OR NEW.status_active_user<>1 OR NEW.status_deleted_user<>0) THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El Super Administrador no puede desactivarse, eliminarse ni cambiar de rol.'; END IF; END$$

CREATE TRIGGER `trg_users_protect_super_before_delete` BEFORE DELETE ON `users` FOR EACH ROW BEGIN IF OLD.id_role_user=1 THEN SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT='El Super Administrador no puede eliminarse.'; END IF; END$$

DELIMITER ;


CREATE TABLE IF NOT EXISTS work_tasks (
 id_task INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(180) NOT NULL,
 requester VARCHAR(150) NOT NULL,
 priority ENUM('Baja','Media','Alta') NOT NULL DEFAULT 'Media',
 status ENUM('Pendiente','En progreso','Completada','Cancelada') NOT NULL DEFAULT 'Pendiente',
 due_date DATE NULL,
 description TEXT NOT NULL,
 result TEXT NOT NULL,
 created_by INT NOT NULL,
 deleted TINYINT NOT NULL DEFAULT 0,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 KEY idx_work_tasks_status_due (deleted,status,due_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
