<?php

namespace Acquia\Orca\Utility;

use Acquia\Orca\Fixture\PackageManager;

/**
 * Provides an interface for managing SUT-related settings.
 */
trait SutSettingsTrait {

  /**
   * The SUT-only flag.
   *
   * @var bool
   */
  private $isSutOnly = FALSE;

  /**
   * The package manager.
   *
   * @var \Acquia\Orca\Fixture\PackageManager|null
   */
  private $packageManager;

  /**
   * The SUT.
   *
   * @var \Acquia\Orca\Fixture\Package|null
   */
  private $sut;

  /**
   * Sets the system under test (SUT).
   *
   * @param string|null $package_name
   *   (Optional) The system under test (SUT) in the form of its package name,
   *   e.g., "drupal/example", or NULL to unset the SUT.
   */
  public function setSut(?string $package_name = NULL): void {
    if (!$this->packageManager) {
      throw new \LogicException(sprintf('%s requires a usable %s.', self::class, PackageManager::class));
    }
    $this->sut = $this->packageManager->get($package_name);
  }

  /**
   * Sets the SUT-only flag.
   *
   * @param bool $is_sut_only
   *   TRUE for SUT-only or FALSE for not.
   */
  public function setSutOnly(bool $is_sut_only): void {
    $this->isSutOnly = $is_sut_only;
  }

}