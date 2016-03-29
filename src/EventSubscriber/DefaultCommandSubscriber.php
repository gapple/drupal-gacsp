<?php
namespace Drupal\gacsp\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\gacsp\AnalyticsCommand\Create;
use Drupal\gacsp\AnalyticsCommand\Linker\AutoLink;
use Drupal\gacsp\AnalyticsCommand\Pageview;
use Drupal\gacsp\AnalyticsCommand\RequirePlugin;
use Drupal\gacsp\AnalyticsEvents;
use Drupal\gacsp\CommandRegistryService;
use Drupal\gacsp\Event\CollectEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class DefaultCommandSubscriber.
 */
class DefaultCommandSubscriber implements EventSubscriberInterface {

  /**
   * The gacsp Command Registry service.
   *
   * @var \Drupal\gacsp\CommandRegistryService
   */
  protected $commandRegistry;

  /**
   * The config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    return [
      AnalyticsEvents::COLLECT => [
        ['onCollectDefaultCommands'],
        ['onCollectRegisteredCommands'],
      ],
    ];
  }

  /**
   * DefaultCommandSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $configFactory
   *   The config factory service.
   * @param \Drupal\gacsp\CommandRegistryService $commandRegistry
   *   The command registry service.
   */
  public function __construct(ConfigFactoryInterface $configFactory, CommandRegistryService $commandRegistry) {
    $this->configFactory = $configFactory;
    $this->commandRegistry = $commandRegistry;
  }

  /**
   * Add commands registered with the command registry service.
   *
   * @param \Drupal\gacsp\Event\CollectEvent $event
   *   The AnalyticsEvents::COLLECT event.
   */
  public function onCollectRegisteredCommands(CollectEvent $event) {
    foreach ($this->commandRegistry->getCommands() as $command) {
      $event->addCommand($command);
    }
  }

  /**
   * Add default events according to configuration.
   *
   * @param \Drupal\gacsp\Event\CollectEvent $event
   *   The AnalyticsEvents::COLLECT event.
   */
  public function onCollectDefaultCommands(CollectEvent $event) {

    $config = $this->configFactory->get('gacsp.settings');

    if ($config->get('add_default_commands')) {
      if (($tracking_id = $config->get('tracking_id'))) {
        $fieldsObject = [];
        if ($config->get('plugins.linker.enable')) {
          $fieldsObject['allowLinker'] = TRUE;
        }

        $event->addCommand(new Create($tracking_id, 'auto', NULL, $fieldsObject));
      }
      if ($config->get('send_pageview')) {
        $event->addCommand(new Pageview());
      }

      if ($config->get('plugins.linkid')) {
        $event->addCommand(new RequirePlugin('linkid'));
      }
      if ($config->get('plugins.displayfeatures')) {
        $event->addCommand(new RequirePlugin('displayfeatures'));
      }
      if ($config->get('plugins.linker.enable')) {
        $event->addCommand(new RequirePlugin('linker'));
        if (($domains = $config->get('plugins.linker.domains'))) {
          $event->addCommand(new AutoLink($domains));
        }
      }
    }
  }

}
