<?php

namespace Acquia\Orca\Domain\Fixture;

use Acquia\Orca\Helper\Filesystem\FixturePathHandler;
use Acquia\Orca\Helper\Process\ProcessRunner;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Removes the fixture.
 */
class FixtureRemover {

  /**
   * The filesystem.
   *
   * @var \Symfony\Component\Filesystem\Filesystem
   */
  private $filesystem;

  /**
   * The fixture path handler.
   *
   * @var \Acquia\Orca\Helper\Filesystem\FixturePathHandler
   */
  private $fixture;

  /**
   * The output decorator.
   *
   * @var \Symfony\Component\Console\Style\SymfonyStyle
   */
  private $output;

  /**
   * The process runner.
   *
   * @var \Acquia\Orca\Helper\Process\ProcessRunner
   */
  private $processRunner;

  /**
   * Constructs an instance.
   *
   * @param \Symfony\Component\Filesystem\Filesystem $filesystem
   *   The filesystem.
   * @param \Acquia\Orca\Helper\Filesystem\FixturePathHandler $fixture_path_handler
   *   The fixture path handler.
   * @param \Symfony\Component\Console\Style\SymfonyStyle $output
   *   The output decorator.
   * @param \Acquia\Orca\Helper\Process\ProcessRunner $process_runner
   *   The process runner.
   */
  public function __construct(Filesystem $filesystem, FixturePathHandler $fixture_path_handler, SymfonyStyle $output, ProcessRunner $process_runner) {
    $this->fixture = $fixture_path_handler;
    $this->filesystem = $filesystem;
    $this->output = $output;
    $this->processRunner = $process_runner;
  }

  /**
   * Removes the fixture.
   */
  public function remove(): void {
    $this->output->section('Removing fixture');
    $this->prepareFilesForDeletion();
    $this->deleteFixtureDirectory();
  }

  /**
   * Prepares files for deletion by setting write permissions.
   *
   * Write permissions on some files are removed by the Drupal installer.
   */
  private function prepareFilesForDeletion(): void {
    $path = $this->fixture->getPath('docroot/sites/default');
    if (!$this->filesystem->exists($path)) {
      return;
    }

    // Filesystem::remove() seems like a better choice than a raw Process, but
    // for reasons unknown, it fails due to file permissions.
    $this->processRunner->runExecutable('chmod', [
      '-R',
      'u+w',
      '.',
    ], $path);
  }

  /**
   * Deletes the entire fixture directory.
   */
  private function deleteFixtureDirectory(): void {
    $root_path = $this->fixture->getPath();
    $this->output->comment("Removing {$root_path}");
    $this->filesystem->remove($root_path);
  }

}
