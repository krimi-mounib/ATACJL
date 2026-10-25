-- ATACJL v6: reliable member-directory compatibility and updated translation provider default.
-- Run after migrate_v5.sql on an existing installation. This only updates known obsolete
-- default values; a deliberately configured provider/API key is preserved.
UPDATE site_settings AS url_setting
LEFT JOIN site_settings AS key_setting ON key_setting.setting_key='translation_api_key'
SET url_setting.setting_value='https://translate.cutie.dating/translate'
WHERE url_setting.setting_key='translation_api_url'
  AND (
    (url_setting.setting_value='https://translate.argosopentech.com/translate' AND COALESCE(key_setting.setting_value,'')='')
    OR (url_setting.setting_value='https://libretranslate.com/translate' AND COALESCE(key_setting.setting_value,'')='')
  );
