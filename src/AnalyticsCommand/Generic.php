<?php
/**
 * @file
 * Contains \Drupal\gacsp\AnalyticsCommand\FieldObject.
 */

namespace Drupal\gacsp\AnalyticsCommand;

/**
 * Class FieldsObject.
 */
class Generic implements DrupalSettingCommandsInterface {

  use DrupalSettingCommandsTrait;

  const DEFAULT_PRIORITY = 0;

  /**
   * The command name.
   *
   * @var string
   */
  protected $command;

  /**
   * The command options.
   *
   * @var array
   */
  protected $fieldsObject;

  /**
   * The tracker name.
   *
   * @var string
   */
  protected $trackerName;

  /**
   * AnalyticsCommand constructor.
   *
   * @param string $command
   *   The command name.
   * @param array $fields_object
   *   An array of values for the command.
   * @param string $tracker_name
   *   The tracker name (optional).
   * @param int $priority
   *   The command priority.
   */
  public function __construct($command, array $fields_object = [], $tracker_name = NULL, $priority = self::DEFAULT_PRIORITY) {
    $this->command = $this->key = $command;
    $this->fieldsObject = $fields_object;
    $this->trackerName = $tracker_name;
    $this->priority = $priority;
  }

  /**
   * Get the command name.
   *
   * @return string
   *   The command name.
   */
  public function getCommand() {
    return $this->command;
  }

  /**
   * An array of values to send with the command.
   *
   * @return array
   *   An array of command values.
   */
  public function getFieldsObject() {
    return $this->fieldsObject;
  }

  public function getTrackerName() {
    return $this->trackerName;
  }

  /**
   * {@inheritdoc}
   */
  public function getSettingCommands() {
    $command = [
      ($this->trackerName ? $this->trackerName . '.' : '') . $this->command,
    ];

    if (!empty($this->fieldsObject)) {
      $command[] = $this->fieldsObject;
    }

    return [$command];
  }
}
