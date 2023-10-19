<?php

namespace Drupal\Tests\replaywebpage\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Core\Extension;

/**
 * Tests the display of the ReplayWebPage module.
 *
 * @group replaywebpage
 */
class DisplayTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stable';

  protected $strictConfigSchema = FALSE;
  
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
    'path',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();
    // create user 
    $permissions = [
      'access media overview',
      'administer media',
      'administer media fields',
      'administer media form display',
      'administer media display',
      'administer media types',
      'view media',
      'access content overview',
      'view all revisions',
      'administer content types',
      'administer node fields',
      'administer node form display',
      'administer node display',
      'bypass node access',
    ];
    $user = $this->drupalCreateUser($permissions);
    $this->drupalLogin($user);

    // set view 
    $this->drupalGet('admin/structure/media/manage/web_archive/display');
    $data = [];
    $data['fields[field_media_file][type]'] = 'replaywebpage_formatter';
    $this->submitForm($data, 'Save');
    $this->assertSession()->pageTextContainsOnce('Your settings have been saved.');

    // upload file 
    $this->drupalGet('media/add/web_archive');
    $data = [];
    $data['name[0][value]'] = 'Test';
    $data['field_base_url[0][uri]'] = 'https://en.wikipedia.org/wiki/Pok%C3%A9mon';
    $path = \Drupal::service('extension.list.module')->getPath('replaywebpage') . '/tests/files/wikipedia.wacz';
    $data['files[field_media_file_0]'] = $path;
    $this->submitForm($data, 'Save');
    $this->assertSession()->pageTextContainsOnce('Web Archive Test has been created.');

    // create content type
    $this->drupalGet('admin/structure/types/add');
    $data = [];
    $data['name'] = 'Content';
    $data['type'] = 'content';
    $this->submitForm($data, 'Save and manage fields');

    // add web archive to content
    $this->drupalGet('admin/structure/types/manage/content/fields/add-field');
    $data = [];
    $data['existing_storage_name'] = 'field_web_archive';
    $data['existing_storage_label'] = 'Web Archive';
    $data['field_name'] = 'web_archive';
    $this->submitForm($data, 'Save and continue');

    // save settings
    $data = [];
    $data['settings[handler_settings][target_bundles][web_archive]'] = 'web_archive';
    $this->submitForm($data, 'Save settings');

    // add media 
    $this->drupalGet('node/add/content');
    $data = [];
    $data['field_web_archive[0][target_id]'] = 'Test';
    $data['title[0][value]'] = 'Test Content';
    $this->submitForm($data, 'Save');

    // set content view 
    $this->drupalGet('admin/structure/types/manage/content/display');
    $data = [];
    $data['fields[field_web_archive][type]'] = 'entity_reference_entity_view';
    $this->submitForm($data, 'Save');
  }

  /**
   * Test that the ReplayWebPage formatter imports required parameters
   */  
  public function testPlayerWarcDisplay() {
    $this->drupalGet('node/1');
    $this->assertSession()->responseContains('~https://cdn.jsdelivr.net/npm/replaywebpage@[\d\.]+/ui.js~');
    $this->assertSession()->responseContains('wikipedia.wacz');
    $this->assertSession()->responseContains('https://en.wikipedia.org/wiki/Pok%C3%A9mon');
  }

}
