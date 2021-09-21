<?php

namespace Drupal\Tests\replaywebpage\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Tests that fields and media appear correctly.
 *
 * @group replaywebpage
 */
class MediaInstallationTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'replaywebpage',
    'field_ui',
    'media',
    'file',
    'field',
    'link',
    'node',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
  }

  /**
   * Test that the Web Archive media type exists 
   */  
  public function testMediaTypeExists() {
    $permissions = [
      'administer media types',
    ];
    $user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($user);
    $this->drupalGet('admin/structure/media');
    $this->assertSession()->elementExists('xpath', '//td[text() = "Web Archive"]');
  }

  /**
   * Test that the ReplayWebPage formatter exists
   */  
  public function testFieldFormatterExists() {
    $permissions = [
      'administer media display',
    ];
    $user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($user);
    $this->drupalGet('admin/structure/media/manage/web_archive/display');
    $options = $this->assertSession()->optionExists('edit-fields-field-media-file-type', 'ReplayWebPage formatter');
  }

}
