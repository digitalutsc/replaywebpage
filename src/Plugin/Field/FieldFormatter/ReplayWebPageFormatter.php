<?php

namespace Drupal\replaywebpage\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\file\Plugin\Field\FieldFormatter\FileFormatterBase;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;

/**
 * Plugin implementation of the 'ReplayWebPage formatter' formatter.
 *
 * @FieldFormatter(
 *   id = "replaywebpage_formatter",
 *   label = @Translation("ReplayWebPage formatter"),
 *   field_types = {
 *     "file"
 *   }
 * )
 */
class ReplayWebPageFormatter extends FileFormatterBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file URL generator.
   *
   * @var \Drupal\Core\File\FileUrlGeneratorInterface
   */
  protected $fileUrlGenerator;

  /**
   * Constructs a ReplayWebPageFormatter object.
   */
  public function __construct(
    $plugin_id,
    $plugin_definition,
    FieldDefinitionInterface $field_definition,
    array $settings,
    $label,
    $view_mode,
    array $third_party_settings,
    EntityTypeManagerInterface $entity_type_manager,
    FileUrlGeneratorInterface $file_url_generator,
  ) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);
    $this->entityTypeManager = $entity_type_manager;
    $this->fileUrlGenerator = $file_url_generator;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager'),
      $container->get('file_url_generator')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'max_width' => 0,
      'max_height' => 600,
      'no_sandbox' => FALSE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $elements['max_width'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum width'),
      '#description' => $this->t('Use 0 to force 100% width'),
      '#default_value' => $this->getSetting('max_width'),
      '#size' => 5,
      '#maxlength' => 5,
      '#field_suffix' => $this->t('pixels'),
      '#min' => 0,
      '#required' => TRUE,
    ];

    $elements['max_height'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum height'),
      '#default_value' => $this->getSetting('max_height'),
      '#size' => 5,
      '#maxlength' => 5,
      '#field_suffix' => $this->t('pixels'),
      '#min' => 0,
      '#required' => TRUE,
    ];

    $elements['no_sandbox'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('No sandbox'),
      '#description' => $this->t('Check to prevent iframe from being wrapped in sandbox. Only check for trusted sites.'),
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [];
    /** @var \Drupal\media\Entity\Media $media */
    $media = $items->getEntity();

    if ($media->hasField('field_base_url') && $media->hasField('field_media_file')) {
      // Get direct path to file.
      $fid = $media->getSource()->getSourceFieldValue($media);
      $file = $this->entityTypeManager->getStorage('file')->load($fid);
      $uri = $file->getFileUri();
      $url = $this->fileUrlGenerator->generateAbsoluteString($uri);

      // Formatting.
      $height = $this->getSetting('max_height');
      $width = $this->getSetting('max_width');
      $noSandbox = $this->getSetting('no_sandbox');

      // Get base URL from media field.
      $fieldBaseUrl = $media->get('field_base_url')[0];
      $baseUrl = is_null($fieldBaseUrl) ? "" : $fieldBaseUrl->getString();

      $element = [
        '#theme' => 'replayweb_template',
        '#playerBase' => $GLOBALS['base_url'] . '/replay/',
        '#baseUrl' => $baseUrl,
        '#warcSource' => $url,
        '#height' => $height == 0 ? "100%" : $height . "px",
        '#width' => $width == 0 ? "100%" : $width . "px",
        '#noSandbox' => $noSandbox ? 'true' : 'false',
      ];
    }

    return $element;
  }

}
