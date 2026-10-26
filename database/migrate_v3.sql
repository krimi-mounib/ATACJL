-- ATACJL schema alignment and design-release maintenance
-- Safe to run on an existing installation; does not alter user/content records.
CREATE TABLE IF NOT EXISTS team (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(150) NOT NULL,
 role VARCHAR(100) NOT NULL,
 bio TEXT NULL,
 created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_team_name (full_name)
) ENGINE=InnoDB;

-- Replace only untouched seed copy; preserve text customized by an administrator.
UPDATE site_settings SET setting_value='Culture, youth and community in action.'
 WHERE setting_key='home_welcome' AND setting_value='Welcome to ATACJL';
UPDATE site_settings SET setting_value='Discover the projects, cultural activities and community initiatives bringing people together through ATACJL.'
 WHERE setting_key='home_intro' AND setting_value='A community platform connecting culture, youth, recreation and civic participation in Hammam Sousse.';

UPDATE site_settings SET setting_value='ATACJL brings people together through cultural animation, youth engagement and recreational activities in Hammam Sousse. Its story is shaped by the people, partnerships and initiatives built over time.'
 WHERE setting_key='about_history' AND setting_value='Tell the story of the organization here.';
