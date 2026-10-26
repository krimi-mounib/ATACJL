-- ATACJL v7 compatibility repair.
-- Back up the database first. Safe for existing installs: only creates missing operational tables.
-- Run after migrate_v6.sql; does not delete or overwrite application records.
CREATE TABLE IF NOT EXISTS contact_submissions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL,
 subject VARCHAR(190) NOT NULL,
 message TEXT NOT NULL,
 status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_contact_status_created (status,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS volunteer_applications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL,
 phone VARCHAR(40) NOT NULL DEFAULT '',
 age INT NOT NULL DEFAULT 0,
 area_of_interest VARCHAR(120) NOT NULL DEFAULT '',
 availability VARCHAR(120) NOT NULL DEFAULT '',
 motivation TEXT NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_volunteer_status_created (status,created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 admin_id BIGINT UNSIGNED NULL,
 action VARCHAR(100) NOT NULL,
 entity VARCHAR(100) NOT NULL,
 entity_id BIGINT UNSIGNED NULL,
 details JSON NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 INDEX idx_audit_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_members (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 event_id BIGINT UNSIGNED NOT NULL,
 member_id BIGINT UNSIGNED NOT NULL,
 assigned_by BIGINT UNSIGNED NULL,
 assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uniq_event_member (event_id,member_id),
 INDEX idx_event_members_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_rsvps (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 event_id BIGINT UNSIGNED NOT NULL,
 user_id BIGINT UNSIGNED NULL,
 name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL,
 registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uniq_event_user (event_id,user_id),
 INDEX idx_rsvp_event (event_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
