<?php
namespace Drupal\gacsp\AnalyticsCommand;

/**
 * Class Pageview.
 */
class Pageview extends Generic {

  const DEFAULT_PRIORITY = -1;

  /**
   * Create constructor.
   *
   * @param array $fields_object
   *   A map of values for the command's fieldsObject parameter.
   * @param string $tracker_name
   *   The tracker name (optional).
   * @param int $priority
   *   The command priority.
   */
  public function __construct($fields_object = [], $tracker_name = NULL, $priority = self::DEFAULT_PRIORITY) {
    parent::__construct('pageview', $fields_object, $tracker_name, $priority);
  }

}
