<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// \https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_angularModules/n
return [
  'js' => [
    'ang/crmSktokens.js',
    'ang/crmSktokens/*.js',
    'ang/crmSktokens/*/*.js',
    'ang/crmSearchAdmin/*/*.js',
  ],
  'partials' => [
    'ang/crmSearchAdmin',
  ],
  'requires' => [
    'crmSearchDisplay',
    'crmUi',
    'ui.bootstrap',
    'crmSearchAdmin',
  ],
  'basePages' => ['civicrm/search', 'civicrm/admin/search'],
  'bundles' => ['bootstrap3'],
  'exports' => [
    'crm-search-display-tokens' => 'E',
  ],
];
