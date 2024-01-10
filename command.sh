// import translation file to ui site configuration
vendor/bin/drush locale:import ar modules/custom/custom_module/translations/ar.po

// drush command to generate custom module 
drush gen module-standard --directory modules/custom --answers '{"name": "module name"}'