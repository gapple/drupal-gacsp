<?php
/**
 * @file
 * Contains \Drupal\gacsp\AnalyticsCommand\GroupInterface.
 */

namespace Drupal\gacsp\AnalyticsCommand;

/**
 * Interface GroupInterface.
 */
interface GroupInterface {

  /**
   * A key to identify this group.
   *
   * @return string
   *   Group name.
   */
  public function getGroupKey();

}
