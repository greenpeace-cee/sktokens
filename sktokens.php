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
    ->addSelect('label', 'settings', 'saved_search_id.name')
    ->addWhere('type', '=', 'tokens')
    ->execute();
  foreach ($searchDisplays as $searchDisplay) {
    $fields = $searchDisplay['settings']['columns'];
    $category = \CRM_Utils_String::titleToVar($searchDisplay['label']);
    foreach ($fields as $field) {
      if ($field['label'] ?? FALSE) {
        $e->entity($category)->register($field['key'], $field['label']);
      }
    }
  }
}

function sktokens_evaluate_tokens(\Civi\Token\Event\TokenValueEvent $e) {
  // Get the list of token SearchDisplays.
  $tokens = $e->getTokenProcessor()->getMessageTokens();
  $searchDisplays = (array) \Civi\Api4\SearchDisplay::get(FALSE)
    ->addSelect('name', 'label', 'saved_search_id.name', 'saved_search_id.api_entity', 'settings')
    ->addWhere('type', '=', 'tokens')
    ->execute();
  foreach ($searchDisplays as $searchDisplay) {
    $rewriteMap = \Civi\Sktokens\Utils::getRewriteMap($searchDisplay['name'], $searchDisplay['settings']['columns']);
    $category = \CRM_Utils_String::titleToVar($searchDisplay['label']);
    // Check if any tokens from each SearchDisplay are used.
    if ($tokens[$category] ?? FALSE) {
      $primaryKeyType = strtolower($searchDisplay['saved_search_id.api_entity']) . 'Id';
      // Get all the row IDs so we can do one query.
      $primaryKeys = [];
      foreach ($e->getRows() as $key => $row) {
        $primaryKeys[] = $e->getTokenProcessor()->getContextValues($primaryKeyType)[$key];
      }
      // Check to see if any of the elements in $primaryKeys has a value.
      $primaryKeyExists = array_filter($primaryKeys);
      // Return if no $primaryKeys have a value.
      if (!$primaryKeyExists) {
        return;
      }
      $searchResult = \Civi\Api4\SearchDisplay::run(FALSE)
        ->setSavedSearch($searchDisplay['saved_search_id.name'])
        ->setFilters(['id' => $primaryKeys])
        ->setDisplay($searchDisplay['name'])
        ->execute();
      if (!count($searchResult)) {
        return;
      }
      $dataArrayUnindexed = array_column((array) $searchResult, 'data');
      $dataArray = array_combine(array_column($dataArrayUnindexed, 'id'), $dataArrayUnindexed);
      $rewriteArrayUnindexed = array_column((array) $searchResult, 'columns');
      $rewriteArray = array_combine(array_column($dataArrayUnindexed, 'id'), $rewriteArrayUnindexed);
      foreach ($e->getRows() as $key => $row) {
        $rowPrimaryKey = $e->getTokenProcessor()->getContextValues($primaryKeyType)[$key];
        foreach ($tokens[$category] as $token) {
          $renderedValue = $dataArray[$rowPrimaryKey][$token];
          // Only rewritten tokens will have a rewrite label.
          $rewriteLabel = $rewriteMap[$token] ?? FALSE;
          if ($rewriteLabel && $rewriteArray) {
            $renderedValue = \Civi\Sktokens\Utils::getRewrittenToken($rewriteLabel, $rewriteArray[$rowPrimaryKey]);
          }
          if ($renderedValue) {
            // GROUP_CONCAT gets returned as an array, not a string, which screws up...well, everything.
            $renderedValue = implode(', ', (array) $renderedValue);
            $row->format('text/html');
            $row->tokens($category, $token, $renderedValue);
          }
        }
      }
    }
  }
}

/**
 * Filters don't work when there's no primary key in the display.
 */
function sktokens_civicrm_pre($op, $objectName, $objectId, &$objectRef) {
  if ($objectName === 'SavedSearch' && in_array($op, ['create', 'edit'])) {
    if (!in_array('id', $objectRef["api_params"]["select"])) {
      $objectRef["api_params"]["select"][] = 'id';
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
