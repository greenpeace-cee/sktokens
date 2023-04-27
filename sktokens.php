<?php

require_once 'sktokens.civix.php';
// phpcs:disable
use CRM_Sktokens_ExtensionUtil as E;
// phpcs:enable
use Symfony\Component\DependencyInjection\ContainerBuilder;

function sktokens_civicrm_container(ContainerBuilder $container) {
  $container->findDefinition('dispatcher')->addMethodCall('addListener', ['civi.token.list', 'sktokens_register_tokens'])->setPublic(TRUE);
  $container->findDefinition('dispatcher')->addMethodCall('addListener', ['civi.token.eval', 'sktokens_evaluate_tokens'])->setPublic(TRUE);
}

function sktokens_register_tokens(\Civi\Token\Event\TokenRegisterEvent $e) {
  $searchDisplays = (array) \Civi\Api4\SearchDisplay::get()
    ->addSelect('name', 'label', 'settings', 'saved_search_id.name')
    ->addWhere('type', '=', 'tokens')
    ->execute();
  foreach ($searchDisplays as $searchDisplay) {
    $fields = $searchDisplay['settings']['columns'];
    $category = CRM_Utils_String::titleToVar($searchDisplay['label']);
    foreach ($fields as $field) {
      if ($field['label']) {
        $e->entity($category)->register($field['key'], $field['label']);
      }
    }
  }
}

function sktokens_evaluate_tokens(\Civi\Token\Event\TokenValueEvent $e) {
  // Get the list of token SearchDisplays.
  $tokens = $e->getTokenProcessor()->getMessageTokens();
  $searchDisplays = (array) \Civi\Api4\SearchDisplay::get()
    ->addSelect('label', 'saved_search_id.name', 'saved_search_id.api_entity')
    ->addWhere('type', '=', 'tokens')
    ->execute();
  foreach ($searchDisplays as $searchDisplay) {
    $category = CRM_Utils_String::titleToVar($searchDisplay['label']);
    // Check if any tokens from each SearchDisplay are used.
    if ($tokens[$category] ?? FALSE) {
      $primaryKeyType = strtolower($searchDisplay['saved_search_id.api_entity']) . 'Id';
      // TODO: Can we do fewer API calls by getting all the IDs at once?
      foreach ($e->getRows() as $row) {
        $primaryKey = $e->getTokenProcessor()->getContextValues($primaryKeyType)[0];
        $searchResult = \Civi\Api4\SearchDisplay::run()
          ->setSavedSearch($searchDisplay['saved_search_id.name'])
          ->setFilters(['id' => $primaryKey])
          ->execute()
          ->first();
        if ($searchResult['data'] ?? FALSE) {
          foreach ($searchResult['data'] as $field => $value) {
            $row->tokens($category, $field, $value);
          }
        }
      }
    }
  }
}

/**
 * Implements hook_civicrm_config().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_config/
 */
function sktokens_civicrm_config(&$config): void {
  _sktokens_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_install
 */
function sktokens_civicrm_install(): void {
  _sktokens_civix_civicrm_install();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_enable
 */
function sktokens_civicrm_enable(): void {
  _sktokens_civix_civicrm_enable();
}

// --- Functions below this ship commented out. Uncomment as required. ---

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_preProcess
 */
//function sktokens_civicrm_preProcess($formName, &$form): void {
//
//}

/**
 * Implements hook_civicrm_navigationMenu().
 *
 * @link https://docs.civicrm.org/dev/en/latest/hooks/hook_civicrm_navigationMenu
 */
//function sktokens_civicrm_navigationMenu(&$menu): void {
//  _sktokens_civix_insert_navigation_menu($menu, 'Mailings', [
//    'label' => E::ts('New subliminal message'),
//    'name' => 'mailing_subliminal_message',
//    'url' => 'civicrm/mailing/subliminal',
//    'permission' => 'access CiviMail',
//    'operator' => 'OR',
//    'separator' => 0,
//  ]);
//  _sktokens_civix_navigationMenu($menu);
//}
