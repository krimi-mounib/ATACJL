CREATE DATABASE IF NOT EXISTS atacjl_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE atacjl_db;

CREATE TABLE users (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(80) NULL UNIQUE,
 full_name VARCHAR(150) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('user','member','admin') NOT NULL DEFAULT 'user',
 email_verified TINYINT(1) NOT NULL DEFAULT 0,
 phone VARCHAR(40) NULL,
 occupation VARCHAR(150) NULL,
 bio TEXT NULL,
 profile_photo VARCHAR(1000) NULL,
 is_active TINYINT(1) NOT NULL DEFAULT 1,
 is_banned TINYINT(1) NOT NULL DEFAULT 0,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 last_login DATETIME NULL,
 INDEX idx_users_role (role), INDEX idx_users_status (is_active,is_banned,email_verified)
) ENGINE=InnoDB;

CREATE TABLE membership_applications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 user_id BIGINT UNSIGNED NOT NULL,
 motivation TEXT NOT NULL,
 status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
 reviewed_by BIGINT UNSIGNED NULL,
 reviewed_at DATETIME NULL,
 admin_note TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_membership_status (status)
) ENGINE=InnoDB;

CREATE TABLE projects (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title_en VARCHAR(255) NOT NULL, title_fr VARCHAR(255) NOT NULL, title_ar VARCHAR(255) NOT NULL,
 title_de VARCHAR(255) NOT NULL, title_it VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL,
 description_en LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, description_ar LONGTEXT NOT NULL,
 description_de LONGTEXT NOT NULL, description_it LONGTEXT NOT NULL, description_es LONGTEXT NOT NULL,
 category VARCHAR(80) NOT NULL, status ENUM('active','completed') NOT NULL DEFAULT 'active',
 featured_image VARCHAR(1000) NULL, start_date DATE NULL, end_date DATE NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_projects_status (status)
) ENGINE=InnoDB;

CREATE TABLE news (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title_en VARCHAR(255) NOT NULL, title_fr VARCHAR(255) NOT NULL, title_ar VARCHAR(255) NOT NULL,
 title_de VARCHAR(255) NOT NULL, title_it VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL,
 body_en LONGTEXT NOT NULL, body_fr LONGTEXT NOT NULL, body_ar LONGTEXT NOT NULL,
 body_de LONGTEXT NOT NULL, body_it LONGTEXT NOT NULL, body_es LONGTEXT NOT NULL,
 category VARCHAR(80) NOT NULL, featured_image VARCHAR(1000) NULL, author_id BIGINT UNSIGNED NULL,
 published_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, is_published TINYINT(1) NOT NULL DEFAULT 1,
 FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_news_published (is_published,published_at)
) ENGINE=InnoDB;

CREATE TABLE events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title_en VARCHAR(255) NOT NULL, title_fr VARCHAR(255) NOT NULL, title_ar VARCHAR(255) NOT NULL,
 title_de VARCHAR(255) NOT NULL, title_it VARCHAR(255) NOT NULL, title_es VARCHAR(255) NOT NULL,
 description_en LONGTEXT NOT NULL, description_fr LONGTEXT NOT NULL, description_ar LONGTEXT NOT NULL,
 description_de LONGTEXT NOT NULL, description_it LONGTEXT NOT NULL, description_es LONGTEXT NOT NULL,
 location VARCHAR(255) NOT NULL, event_date DATE NOT NULL, event_time TIME NOT NULL, capacity INT NULL,
 featured_image VARCHAR(1000) NULL, is_published TINYINT(1) NOT NULL DEFAULT 1,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE event_rsvps (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, event_id BIGINT UNSIGNED NOT NULL, user_id BIGINT UNSIGNED NULL,
 name VARCHAR(150) NOT NULL, email VARCHAR(190) NOT NULL, registered_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE, FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
 UNIQUE KEY uniq_event_user (event_id,user_id), INDEX idx_rsvp_event(event_id)
) ENGINE=InnoDB;

CREATE TABLE event_members (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, event_id BIGINT UNSIGNED NOT NULL, member_id BIGINT UNSIGNED NOT NULL,
 assigned_by BIGINT UNSIGNED NULL, assigned_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE, FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE,
 FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL, UNIQUE KEY uniq_event_member(event_id,member_id)
) ENGINE=InnoDB;

CREATE TABLE gallery_albums (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, title VARCHAR(255) NOT NULL, category VARCHAR(80) NOT NULL DEFAULT 'General',
 description TEXT NULL, featured_item_id BIGINT UNSIGNED NULL, created_by BIGINT UNSIGNED NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE gallery_items (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, album_id BIGINT UNSIGNED NULL, image_path VARCHAR(1000) NOT NULL,
 caption_en VARCHAR(255) NULL, caption_fr VARCHAR(255) NULL, caption_ar VARCHAR(255) NULL, category VARCHAR(80) NOT NULL DEFAULT 'General',
 uploaded_by BIGINT UNSIGNED NULL, uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (album_id) REFERENCES gallery_albums(id) ON DELETE SET NULL, FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL,
 INDEX idx_gallery_album(album_id)
) ENGINE=InnoDB;
ALTER TABLE gallery_albums ADD CONSTRAINT fk_gallery_featured FOREIGN KEY (featured_item_id) REFERENCES gallery_items(id) ON DELETE SET NULL;

CREATE TABLE likes (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL,
 content_type ENUM('project','news','event','gallery') NOT NULL, content_id BIGINT UNSIGNED NOT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY uniq_like (user_id,content_type,content_id), FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
 INDEX idx_like_content (content_type,content_id)
) ENGINE=InnoDB;

CREATE TABLE comments (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, user_id BIGINT UNSIGNED NOT NULL,
 content_type ENUM('project','news','event','gallery') NOT NULL, content_id BIGINT UNSIGNED NOT NULL,
 body TEXT NOT NULL, status ENUM('visible','hidden','pending') NOT NULL DEFAULT 'visible',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NULL,
 FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE, INDEX idx_comments_content (content_type,content_id,status)
) ENGINE=InnoDB;

CREATE TABLE participation_claims (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, member_id BIGINT UNSIGNED NOT NULL, event_id BIGINT UNSIGNED NOT NULL,
 participation_date DATE NOT NULL, details TEXT NULL,
 status ENUM('pending','verified','rejected') NOT NULL DEFAULT 'pending', verified_by BIGINT UNSIGNED NULL, verified_at DATETIME NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (member_id) REFERENCES users(id) ON DELETE CASCADE, FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
 FOREIGN KEY (verified_by) REFERENCES users(id) ON DELETE SET NULL, UNIQUE KEY uniq_member_event (member_id,event_id)
) ENGINE=InnoDB;

CREATE TABLE contact_submissions (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, name VARCHAR(150) NOT NULL, email VARCHAR(190) NOT NULL,
 subject VARCHAR(190) NOT NULL, message TEXT NOT NULL, status ENUM('new','read','replied') NOT NULL DEFAULT 'new',
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE volunteer_applications (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(150) NOT NULL, email VARCHAR(190) NOT NULL,
 phone VARCHAR(40) NOT NULL, age INT NOT NULL, area_of_interest VARCHAR(120) NOT NULL, availability VARCHAR(120) NOT NULL,
 motivation TEXT NOT NULL, status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE newsletter_subscribers (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, email VARCHAR(190) NOT NULL UNIQUE, language_preference VARCHAR(8) NOT NULL DEFAULT 'en',
 is_active TINYINT(1) NOT NULL DEFAULT 1, subscribed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE team (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(150) NOT NULL,
 role VARCHAR(100) NOT NULL,
 bio TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_team_name (full_name)
) ENGINE=InnoDB;

CREATE TABLE site_settings (
 setting_key VARCHAR(100) PRIMARY KEY, setting_value LONGTEXT NOT NULL, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE audit_logs (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, admin_id BIGINT UNSIGNED NULL, action VARCHAR(100) NOT NULL, entity VARCHAR(100) NOT NULL,
 entity_id BIGINT UNSIGNED NULL, details JSON NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL, INDEX idx_audit_created (created_at)
) ENGINE=InnoDB;

-- Administrator accounts are created securely with scripts/create-admin.php (CLI only).

INSERT INTO site_settings(setting_key,setting_value) VALUES
('site_name','ATACJL'),
('site_tagline','Culture · Youth · Community'),
('site_full_name','Tunisian Association for Cultural Animation, Youth and Recreation in Hammam Sousse'),
('address','Hammam Sousse, Sousse, Tunisia'),
('phone','+216 00 000 000'),
('email','contact@atacjl.tn'),
('social_facebook',''),('social_instagram',''),('social_youtube',''),('map_embed_url',''),
('home_welcome','Culture, youth and community in action.'),
('home_intro','Discover the projects, cultural activities and community initiatives bringing people together through ATACJL.'),
('about_history','ATACJL brings people together through cultural animation, youth engagement and recreational activities in Hammam Sousse. Its story is shaped by the people, partnerships and initiatives built over time.'),
('about_mission','Build inclusive cultural, youth and recreational opportunities with the community.'),
('about_values','Culture, youth development, participation, inclusion and recreation.'),
('home_eyebrow','ATACJL · Hammam Sousse, Tunisia'),
('home_cta_primary','Explore our projects'),
('home_cta_secondary','Get involved'),
('home_trust_line','A local community. Shared ideas. Meaningful participation.'),
('home_hero_note_one_title','Culture & creativity'),
('home_hero_note_one_text','Ideas brought to life together'),
('home_hero_note_two_title','Youth & participation'),
('home_hero_note_two_text','Make room for every voice'),
('home_stats_label_members','Active members'),
('home_stats_label_projects','Projects'),
('home_stats_label_events','Upcoming events'),
('home_stats_label_news','Published stories'),
('home_projects_eyebrow','Our work'),
('home_projects_title','Projects shaped by community.'),
('home_projects_intro','Explore initiatives and activities that create opportunities for culture, youth and shared experiences.'),
('home_projects_link','All projects'),
('home_news_eyebrow','Latest stories'),
('home_news_title','News from our community.'),
('home_news_intro','Updates, highlights and stories from the work taking place around ATACJL.'),
('home_news_link','All news'),
('home_events_eyebrow','Come together'),
('home_events_title','Be part of what''s next.'),
('home_events_intro','Take part in an event, meet the community, or contribute your time and ideas.'),
('home_events_link','View all events'),
('home_cta_eyebrow','Participate'),
('home_cta_title','Bring your time, skills and ideas.'),
('home_cta_intro','Communities grow when people take part. Find a way to contribute that fits you.'),
('home_cta_member_link','Become a member'),
('home_cta_volunteer_link','Volunteer with us'),
('about_intro','A community-oriented association in Hammam Sousse, bringing culture, youth engagement and recreation together through participation.'),
('about_purpose_eyebrow','OUR PURPOSE'),
('about_purpose_title','Creating space for people, ideas and shared experiences.'),
('about_purpose_text','ATACJL is a place to connect, contribute and take part in activities that strengthen community life.'),
('about_story_title','Our story'),
('about_mission_title','Our mission'),
('about_values_title','Our values'),
('about_cta_title','Interested in what we do?'),
('about_cta_text','Explore current projects or speak with the association about joining in.'),
('about_cta_projects_link','Explore projects'),
('about_cta_contact_link','Contact ATACJL'),
('projects_eyebrow','OUR WORK'),
('projects_title','Projects and initiatives.'),
('projects_intro','Discover initiatives creating opportunities for culture, youth and shared experiences.'),
('news_eyebrow','STORIES & UPDATES'),
('news_title','News from ATACJL.'),
('news_intro','Highlights, announcements and stories from the people and activities that make our community.'),
('events_eyebrow','MEET · LEARN · PARTICIPATE'),
('events_title','Events and activities.'),
('events_intro','Find a workshop, gathering or community activity and discover how to take part.'),
('gallery_eyebrow','MOMENTS & MEMORIES'),
('gallery_title','ATACJL in pictures.'),
('gallery_intro','A look at the people, gatherings and activities that bring the community together.'),
('contact_eyebrow','GET IN TOUCH'),
('contact_title','Talk to ATACJL.'),
('contact_intro','Have a question about a project, event, membership or volunteering? Send us a message and the team will review it.'),
('contact_form_title','Send a message'),
('contact_form_hint','Fields marked as required must be completed.'),
('contact_info_eyebrow','CONTACT DETAILS'),
('contact_info_title','We''re here to help.'),
('translation_api_url','https://translate.cutie.dating/translate'),
('translation_api_key','')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
