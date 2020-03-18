<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_paragraphs\Behat;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\Tests\oe_paragraphs\Traits\UtilityTrait;

/**
 * Defines generic step definitions.
 */
class DrupalContext extends RawDrupalContext {

  use UtilityTrait;

  /**
   * Assert that certain fields are present on the page.
   *
   * @param string $fields
   *   Fields.
   *
   * @throws \Exception
   *   Thrown when an expected field is not present.
   *
   * @Then (the following )field(s) should be present :fields
   */
  public function assertFieldsPresent($fields): void {
    $fields = $this->explodeCommaSeparatedStepArgument($fields);
    $page = $this->getSession()->getPage();
    $not_found = [];
    foreach ($fields as $field) {
      $is_found = $page->findField($field);
      if (!$is_found) {
        $not_found[] = $field;
      }
    }
    if ($not_found) {
      throw new \Exception("Field(s) expected, but not found: " . implode(', ', $not_found));
    }
  }

  /**
   * Assert that certain fields are not present on the page.
   *
   * @param string $fields
   *   Fields.
   *
   * @throws \Exception
   *   Thrown when a column name is incorrect.
   *
   * @Then (the following )field(s) should not be present :fields
   */
  public function assertFieldsNotPresent($fields): void {
    $fields = $this->explodeCommaSeparatedStepArgument($fields);
    $page = $this->getSession()->getPage();
    foreach ($fields as $field) {
      $is_found = $page->findField($field);
      if ($is_found) {
        throw new \Exception("Field should not be found, but is present: " . $field);
      }
    }
  }

  /**
   * Checks that a given image is present on the page.
   *
   * @param string $filename
   *   The image filename.
   *
   * @Then I (should )see the image :filename
   */
  public function assertImagePresent(string $filename): void {
    // Drupal appends an underscore and a number to the filename when duplicate
    // files are uploaded, for example when a test runs more then once.
    // We split up the filename and extension and match for both.
    $parts = pathinfo($filename);
    $extension = $parts['extension'];
    $filename = $parts['filename'];
    $this->assertSession()->elementExists('css', "img[src*='.$extension'][src*='$filename']");
  }

}