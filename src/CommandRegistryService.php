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
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The registered analytics commands.
   *
   * @var array
   */
  protected $commands;

  /**
   * CommandRegistryService constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory service.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;

    $this->commands = [];
  }

  /**
   * Get the configured tracker id.
   *
   * @return string|null
   *   The tracker id.
   */
  public function getTrackingId() {
    $config = $this->configFactory->get('gacsp.settings');

    return $config->get('tracking_id');
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
