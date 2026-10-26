USE atacjl_db;

ALTER TABLE users ADD COLUMN username VARCHAR(80) NULL UNIQUE AFTER id;
ALTER TABLE users ADD COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER role;
ALTER TABLE users ADD COLUMN is_banned TINYINT(1) NOT NULL DEFAULT 0 AFTER is_active;
ALTER TABLE users MODIFY profile_photo VARCHAR(1000) NULL;
ALTER TABLE projects MODIFY featured_image VARCHAR(1000) NULL;
ALTER TABLE news MODIFY featured_image VARCHAR(1000) NULL;
ALTER TABLE events MODIFY featured_image VARCHAR(1000) NULL;

ALTER TABLE likes MODIFY content_type ENUM('project','news','event','gallery') NOT NULL;
ALTER TABLE comments MODIFY content_type ENUM('project','news','event','gallery') NOT NULL;

CREATE TABLE IF NOT EXISTS event_members (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,event_id BIGINT UNSIGNED NOT NULL,member_id BIGINT UNSIGNED NOT NULL,assigned_by BIGINT UNSIGNED NULL,assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE,FOREIGN KEY(member_id) REFERENCES users(id) ON DELETE CASCADE,FOREIGN KEY(assigned_by) REFERENCES users(id) ON DELETE SET NULL,UNIQUE KEY uniq_event_member(event_id,member_id)
) ENGINE=InnoDB;

ALTER TABLE gallery_albums ADD COLUMN category VARCHAR(80) NOT NULL DEFAULT 'General' AFTER title;
ALTER TABLE gallery_albums ADD COLUMN description TEXT NULL AFTER category;
ALTER TABLE gallery_albums ADD COLUMN featured_item_id BIGINT UNSIGNED NULL AFTER description;
ALTER TABLE gallery_albums ADD CONSTRAINT fk_gallery_featured FOREIGN KEY(featured_item_id) REFERENCES gallery_items(id) ON DELETE SET NULL;
ALTER TABLE gallery_items MODIFY image_path VARCHAR(1000) NOT NULL;

-- Do not insert a predictable default administrator. Use php scripts/create-admin.php (CLI only).

INSERT IGNORE INTO site_settings(setting_key,setting_value) VALUES
('social_facebook',''),('social_instagram',''),('social_youtube',''),('map_embed_url',''),('home_welcome','Culture, youth and community in action.'),('home_intro','Discover the projects, cultural activities and community initiatives bringing people together through ATACJL.'),('about_history','ATACJL brings people together through cultural animation, youth engagement and recreational activities in Hammam Sousse. Its story is shaped by the people, partnerships and initiatives built over time.'),('about_mission','Build inclusive cultural, youth and recreational opportunities with the community.'),('about_values','Culture, youth development, participation, inclusion and recreation.');
