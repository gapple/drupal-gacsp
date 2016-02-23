<?php
/**
 * @file
 * Contains \Drupal\gacsp\CommandRegistryService.
 */

namespace Drupal\gacsp;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\gacsp\AnalyticsCommand\DrupalSettingCommandsInterface;

/**
 * Class CommandRegistryService.
 */
class CommandRegistryService {

  /**
   * The registered analytics commands.
   *
   * @var array
   */
  protected $commands;

  /**
   * CommandRegistryService constructor.
   */
  public function __construct() {
    $this->commands = [];
  }

  /**
   * Add an item to the registry.
   *
   * @param \Drupal\gacsp\AnalyticsCommand\DrupalSettingCommandsInterface $command
   *   An analytics command.
   */
  public function addItem(DrupalSettingCommandsInterface $command) {
    $this->commands[] = $command;
  }

  /**
   * Format the commands for use in drupalSettings.
   *
   * @return array
   *   An array of commands for use in drupalSettings.
   */
  public function getDrupalSettingCommands() {
    usort($this->commands, function (DrupalSettingCommandsInterface $a, DrupalSettingCommandsInterface $b) {
      return $b->getPriority() - $a->getPriority();
    });

    return array_reduce(
      $this->commands,
      function ($carry, DrupalSettingCommandsInterface $item) {
        return array_merge($carry, $item->getSettingCommands());
      },
      []
    );
  }
}
