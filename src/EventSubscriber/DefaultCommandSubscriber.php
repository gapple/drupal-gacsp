<?php

namespace Drupal\gacsp\EventSubscriber;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\gacsp\AnalyticsCommand\Create;
use Drupal\gacsp\AnalyticsCommand\Linker\AutoLink;
use Drupal\gacsp\AnalyticsCommand\Pageview;
use Drupal\gacsp\AnalyticsCommand\RequirePlugin;
use Drupal\gacsp\AnalyticsCommand\Set;
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
   * The current user service.
   *
   * @var \Drupal\Core\Session\AccountInterface;
   */
  protected $currentUser;

  /**
   * User Entity Storage.
   *
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $userStorage;

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
  public function __construct(
    ConfigFactoryInterface $configFactory,
    CommandRegistryService $commandRegistry,
    AccountInterface $currentUser,
    EntityTypeManagerInterface $entityTypeManager
  ) {
    $this->configFactory = $configFactory;
    $this->commandRegistry = $commandRegistry;
    $this->currentUser = $currentUser;
    $this->userStorage = $entityTypeManager->getStorage('user');
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

      // Check user role restrictions.
      if (!$this->checkRole(
        $config->get('user_roles.mode'),
        $config->get('user_roles.roles'),
        $this->currentUser->getRoles()
      )) {
        return;
      }

      // Initialize tracker or set tracker options.
      if (($tracking_id = $config->get('tracking_id'))) {
        // Add options which can be provided when initializing the tracker.
        $fieldsObject = [];

        if ($config->get('plugins.linker.enable')) {
          $fieldsObject['allowLinker'] = TRUE;
        }

        if ($config->get('track_user_id') && $this->currentUser->isAuthenticated()) {
          $account = $this->userStorage->load($this->currentUser->id());
          $fieldsObject['userId'] = $account->uuid();
        }

        if ($config->get('anonymize_ip')) {
          $fieldsObject['anonymizeIp'] = TRUE;
        }

        $event->addCommand(new Create($tracking_id, 'auto', NULL, $fieldsObject));
      }
      else {
        // If a trackingId isn't provided for initializing a tracker, these
        // options can be provided via set commands instead.
        if ($config->get('track_user_id') && $this->currentUser->isAuthenticated()) {
          $account = $this->userStorage->load($this->currentUser->id());
          $event->addCommand(new Set('userId', $account->uuid()));
        }

        if ($config->get('anonymize_ip')) {
          $event->addCommand(new Set('anonymizeIp', TRUE));
        }
      }

      if ($config->get('send_pageview')) {
        $event->addCommand(new Pageview());
      }

      // Enable Plugins.
      if ($config->get('plugins.linkid')) {
        $event->addCommand(new RequirePlugin('linkid'));
      }
      if ($config->get('plugins.displayfeatures')) {
        $event->addCommand(new RequirePlugin('displayfeatures'));
      }
      if ($config->get('plugins.linker.enable')) {
        // Note: 'allowLinker' must be set when creating the tracker for this
        // plugin to have an effect.
        $event->addCommand(new RequirePlugin('linker'));
        if (($domains = $config->get('plugins.linker.domains'))) {
          $event->addCommand(new AutoLink($domains));
        }
      }
    }
  }

  /**
   * Check role restrictions.
   *
   * @param string $mode
   *   The role mode; either 'include' or 'exclude'.
   * @param array $limitRoles
   *   An array of roles to check against.
   * @param array $userRoles
   *   The array of roles a user belongs to.
   *
   * @return bool
   *   TRUE if the provided restriction is passed.
   */
  private function checkRole($mode, array $limitRoles, array $userRoles) {

    switch ($mode) {
      case 'disabled':
        return TRUE;

      case 'include':
        $userRoleMode = TRUE;
        break;

      case 'exclude':
        $userRoleMode = FALSE;
        break;

      default:
        throw new \InvalidArgumentException("Mode must be one of 'include' or 'exclude'");
    }

    $userRoleCommon = array_intersect($limitRoles, $userRoles);

    if ($userRoleMode ^ !empty($userRoleCommon)) {
      return FALSE;
    }

    return TRUE;
  }

}
