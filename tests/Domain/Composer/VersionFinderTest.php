<?php

namespace Acquia\Orca\Tests\Domain\Composer;

use Acquia\Orca\Domain\Composer\Version\VersionFinder;
use Acquia\Orca\Exception\VersionNotFoundException;
use Composer\Package\PackageInterface;
use Composer\Package\Version\VersionSelector;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/**
 * @property \Composer\Package\PackageInterface|\Prophecy\Prophecy\ObjectProphecy $bestCandidate
 * @property \Composer\Package\Version\VersionSelector|\Prophecy\Prophecy\ObjectProphecy $versionSelector
 * @coversDefaultClass \Acquia\Orca\Domain\Composer\Version\VersionFinder
 */
class VersionFinderTest extends TestCase {

  protected function setUp(): void {
    $this->versionSelector = $this->prophesize(VersionSelector::class);
  }

  private function createVersionFinder(): VersionFinder {
    $version_selector = $this->versionSelector->reveal();
    return new VersionFinder($version_selector);
  }

  /**
   * @dataProvider providerFindLatestVersion
   *
   * @covers ::findLatestVersion
   */
  public function testFindLatestVersion($package_name, $constraint, $dev, $stability, $expected): void {
    $candidate = $this->prophesize(PackageInterface::class);
    $candidate->getPrettyVersion()
      ->willReturn($expected);
    $this->versionSelector
      ->findBestCandidate($package_name, $constraint, NULL, $stability)
      ->shouldBeCalledOnce()
      ->willReturn($candidate->reveal());
    $finder = $this->createVersionFinder();

    $actual = $finder->findLatestVersion($package_name, $constraint, $dev);

    self::assertSame($expected, $actual);
  }

  public function providerFindLatestVersion() {
    return [
      ['lorem/ipsum', '1.0.0', TRUE, 'dev', '1.0.0'],
      ['dolor/sit', '>1 <2', FALSE, 'alpha', '1.2.3'],
    ];
  }

  public function testFindLatestVersionNoMatch() {
    $this->versionSelector
      ->findBestCandidate(Argument::any(), Argument::any(), NULL, Argument::any())
      ->shouldBeCalledOnce()
      ->willReturn(FALSE);
    $this->expectExceptionObject(new VersionNotFoundException('No available version could be found for "test/example:~1".'));
    $finder = $this->createVersionFinder();

    $finder->findLatestVersion('test/example', '~1', FALSE);
  }

}