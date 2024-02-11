# download rector package
composer require --dev palantirnet/drupal-rector
# add configuration file for rector 
# Configuration: Create a configuration file (e.g., rector.yaml) in your Drupal root directory:
# Use rules relevant to your upgrade path
rules:
- Palantir\Rector\Set\Drupal8\UsePhpUnitTestCaseFromNamespace: true
- Palantir\Rector\Set\Drupal8\ClassUseFormValidatorToFormBase: true
# Dry Run: Test the changes without applying them using:
vendor/bin/rector process modules/custom --dry-run
# Apply Changes: If the dry run looks good, apply the updates:
vendor/bin/rector process modules/custom