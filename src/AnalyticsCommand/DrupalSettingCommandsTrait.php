<?php
/**
 * @file
 * Contains \Drupal\gacsp\AnalyticsCommand\SettingFragmentTrait.
 */

namespace Drupal\gacsp\AnalyticsCommand;

/**
 * Trait AnalyticsCommandSetTrait.
 *
 * Trait for implementing DrupalSettingCommandsInterface.
 */
trait DrupalSettingCommandsTrait {

  /**
   * A key to identify this item.
   *
   * Value does not need to be unique amongst all command instances.
   *
   * @var string
   */
  protected $key;

  /**
   * Priority integer.
   *
   * @var int
   */
  protected $priority;

  /**
   * Get the key identifying this command.
   *
   * @return string
   *   The key string.
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * An integer value for sorting by priority.
   *
   * @return int
   *   The priority value.
   */
  public function getPriority() {
    return $this->priority;
  }

  /**
   * An array of commands to be sent to Google Analytics.
   *
   * @return array
   *   An array of command values.
   */
  public function getSettingCommands() {
    return [];
  }
}
