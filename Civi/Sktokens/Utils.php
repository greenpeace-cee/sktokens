<?php

namespace Civi\Sktokens;

class Utils {

  private static $rewriteMap = [];

  public static function getRewriteMap(string $name, array $columns) : array {
    if (!(self::$rewriteMap[$name] ?? FALSE)) {
      foreach ($columns as $column) {
        if ($column['rewrite'] ?? FALSE) {
          self::$rewriteMap[$name][$column['key']] = $column['label'];
        }
      }
    }
    return self::$rewriteMap[$name] ?? [];
  }

  /**
   * This gets the rewritten value for a single result.
   */
  public static function getRewrittenToken(string $rewriteLabel, array $searchResult) : ?string {
    // This maps token labels to the rendered value.
    foreach ($searchResult['columns'] as $column) {
      if ($column['label']) {
        $lookupArray[$column['label']] = $column['val'];
      }
    }
    return $lookupArray[$rewriteLabel] ?? NULL;
  }

}
