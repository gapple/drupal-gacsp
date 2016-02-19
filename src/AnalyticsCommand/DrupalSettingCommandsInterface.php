<?php
/**
 * @file
 * Contains \Drupal\gacsp\AnalyticsCommand\SettingFragmentInterface.
 */

namespace Drupal\gacsp\AnalyticsCommand;

/**
 * Interface AnalyticsCommandSetInterface.
 */
interface DrupalSettingCommandsInterface {

  /**
   * A key to identify this item.
   *
   * Value does not need to be unique amongst all command instances.
   *
   * @return string
   *   Item name.
   */
  public function getKey();

  /**
   * An integer value for sorting by priority.
   *
   * @return int
   *   The priority value.
   */
  public function getPriority();

  /**
   * An array of commands to be sent to Google Analytics.
   *
   * @return array
   *   An array of command values.
   */
  public function getSettingCommands();
}
