<?php
use CRM_Sktokens_ExtensionUtil as E;
return [
  [
    'name' => 'SearchDisplayType:tokens',
    'entity' => 'OptionValue',
    'params' => [
      'version' => 4,
      'values' => [
        'option_group_id.name' => 'search_display_type',
        'value' => 'tokens',
        'name' => 'crm-search-display-tokens',
        'label' => E::ts('Tokens'),
        'icon' => 'fa-commenting-o',
      ],
      'match' => ['option_group_id', 'name'],
    ],
  ],
];
