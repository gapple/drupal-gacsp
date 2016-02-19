<?php
/**
 * @file
 * Contains \Drupal\gacsp\CommandRegistryService.
 */

namespace Drupal\gacsp;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\gacsp\AnalyticsCommand\Create;
use Drupal\gacsp\AnalyticsCommand\DrupalSettingCommandsInterface;
use Drupal\gacsp\AnalyticsCommand\Group;
use Drupal\gacsp\AnalyticsCommand\Pageview;
use Drupal\gacsp\AnalyticsCommand\Set;

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

    $this->registerInitialCommands();
  }

  /**
   * Register the initial create and pageview commands.
   */
  protected function registerInitialCommands() {

    if (($trackingId = $this->getTrackerId())) {
      $this->commands[] = new Create($trackingId);
      $this->commands[] = new Pageview();
    }
  }

  /**
   * Get the configured tracker id.
   *
   * @return string|null
   *   The tracker id.
   */
  public function getTrackerId() {
    $config = $this->configFactory->get('gacsp.settings');

    return $config->get('tracking_id');
  }

  /**
   * Add a command to the registry.
   *
   * @param \Drupal\gacsp\AnalyticsCommand\DrupalSettingCommandsInterface $command
   *   An analytics command.
   */
  public function addCommand(DrupalSettingCommandsInterface $command) {
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
