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
  $searchDisplays = \Civi\Api4\SearchDisplay::get(FALSE)
    ->addSelect('label', 'settings', 'saved_search_id.name')
    ->addWhere('type', '=', 'tokens')
    ->execute()
    ->getArrayCopy();
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
  $searchDisplays = \Civi\Api4\SearchDisplay::get(FALSE)
    ->addSelect('name', 'label', 'saved_search_id.name', 'saved_search_id.api_entity', 'settings')
    ->addWhere('type', '=', 'tokens')
    ->execute()
    ->getArrayCopy();
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
        ->execute()
        ->getArrayCopy();
      if (empty($searchResult)) {
        return;
      }
      $dataArrayUnindexed = array_column($searchResult, 'data');
      $dataArray = array_combine(array_column($dataArrayUnindexed, 'id'), $dataArrayUnindexed);
      $rewriteArrayUnindexed = array_column($searchResult, 'columns');
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
    // Sometimes api_params is not set. Eg. when using in-place edit to update description via searchkit listings.
    // Have not investigated further but added the below isset/return to stop PHP Fatal error on undefined array key.
    if (!isset($objectRef['api_params'])) {
      return;
    }
    if (!in_array('id', $objectRef['api_params']['select'])) {
      $objectRef['api_params']['select'][] = 'id';
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
