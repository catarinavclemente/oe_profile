<?php

declare(strict_types = 1);

namespace Drupal\Tests\oe_paragraphs\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;

/**
 * Tests paragraphs forms.
 */
class ParagraphsTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'node',
    'field_ui',
    'oe_paragraphs',
    'oe_paragraphs_icon_options_event_test',
  ];

  /**
   * The administration theme name.
   *
   * @var string
   */
  protected $adminTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * A user with administration permissions.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->adminUser = $this->drupalCreateUser([
      'administer content types',
    ]);
    $this->drupalLogin($this->adminUser);
    $this->drupalCreateContentType([
      'type' => 'paragraphs_test',
      'name' => 'Paragraphs Test',
    ]);
    $this->addParagraphsField();
  }

  /**
   * Test Facts and figures paragraphs form.
   */
  public function testIconOptionsEventsubscriber(): void {
    $this->drupalGet('/node/add/paragraphs_test');
    $page = $this->getSession()->getPage();
    $page->pressButton('Add Fact');

    $this->assertSession()->fieldExists('oe_paragraphs[0][subform][field_oe_icon]');
    $allowed_values = [
      '_none',
      'item-test-1',
      'item-test-2',
      'item-test-3',
    ];
    foreach ($allowed_values as $allowed_value) {
      $this->assertSession()->elementsCount('css', 'option[value="' . $allowed_value . '"]', 1);
    }
    $this->assertSession()->elementsCount('css', 'select#edit-oe-paragraphs-0-subform-field-oe-icon option', 4);

  }

  /**
   * Creates content type with paragraphs field.
   */
  protected function addParagraphsField(): void {
    $field_storage = FieldStorageConfig::create([
      'field_name' => 'oe_paragraphs',
      'entity_type' => 'node',
      'type' => 'entity_reference_revisions',
      'cardinality' => '-1',
      'settings' => [
        'target_type' => 'paragraph',
      ],
    ]);
    $field_storage->save();
    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => 'paragraphs_test',
      'settings' => [
        'handler' => 'default:paragraph',
        'handler_settings' => ['target_bundles' => NULL],
      ],
    ]);
    $field->save();

    $form_display = $this->container->get('entity_display.repository')->getFormDisplay('node', 'paragraphs_test');
    $form_display = $form_display->setComponent('oe_paragraphs', ['type' => 'paragraphs']);
    $form_display->save();
  }

}
