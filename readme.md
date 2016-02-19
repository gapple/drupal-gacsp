gascp Module
-------------------

gascp provides Google Analytics integration in Drupal that emphasises
*programmability* over *configurability*.

No inline Javascript is used, to not inhibit the use of
[CSP (Content Security Policy)](https://developer.mozilla.org/en-US/docs/Web/Security/CSP)
for the prevention of XSS attacks.

## Usage

Provide a Google Analytics Tracking ID in the module configuration, and the
tracker and a pageview event will automatically be added to the page.

Additional analytics commands can be added via the `gacsp.command_registry` 
service.

    \Drupal::service('gascp.command_registry')->addCommand(
      new \Drupal\gacsp\AnalyticsCommand\Set('dimension1', 'value')
    );


### Ordering Commands

Each command has a priority that determines the order that it is added to the 
command queue.  The default values are

 - Create: 300
 - Set: 100
 - Generic: 0
 - Pageview: -1


### Grouping Commands

Commands can be placed within a group to maintain a consistent order among other
 commands.

    $commandGroup = new \Drupal\gacsp\AnalyticsCommand\Group('dimensions', \Drupal\gacsp\AnalyticsCommand\Set::DEFAULT_PRIORITY);
    $commandGroup->addCommand(
      new \Drupal\gacsp\AnalyticsCommand\Set('dimension1', 'value')
    );
    $commandGroup->addCommand(
      new \Drupal\gacsp\AnalyticsCommand\Set('dimension2', 'value')
    );
    \Drupal::service('gascp.command_registry')->addCommand($commandGroup);
