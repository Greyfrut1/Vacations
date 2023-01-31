<?php

namespace Drupal\twig_field\Plugin\Field\FieldWidget;

use Drupal\codemirror_editor\CodeMirrorPluginTrait;
use Drupal\Core\Entity\Entity\EntityViewDisplay;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Defines the Twig field widget.
 *
 * @FieldWidget(
 *   id = "twig",
 *   label = @Translation("Template editor"),
 *   field_types = {"twig"},
 * )
 */
class TwigWidget extends WidgetBase {

  use CodeMirrorPluginTrait;

  public const REQUIRED_CODEMIRROR_MODES = ['xml', 'twig', 'javascript', 'css'];

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings = [
      'rows' => '5',
      'placeholder' => '',
      'mode' => 'html_twig',
    ];
    return $settings + self::getDefaultCodeMirrorSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $element['rows'] = [
      '#type' => 'number',
      '#title' => $this->t('Rows'),
      '#default_value' => $this->getSetting('rows'),
      '#required' => TRUE,
      '#min' => 1,
    ];

    $element['placeholder'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Placeholder'),
      '#default_value' => $this->getSetting('placeholder'),
      '#description' => $this->t('Text that will be shown inside the field until a value is entered.'),
    ];

    $element += self::buildCodeMirrorSettingsForm($this->getSettings());
    unset($element['mode']);
    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $settings = $this->getSettings();
    $summary[] = $this->t('Number of rows: @rows', ['@rows' => $settings['rows']]);
    if ($settings['placeholder'] != '') {
      $summary[] = $this->t('Placeholder: @placeholder', ['@placeholder' => $settings['placeholder']]);
    }

    $summary[] = $this->t('Load toolbar: @toolbar', ['@toolbar' => $this->formatBoolean('toolbar')]);
    $summary[] = $this->t('Line wrapping: @lineWrapping', ['@lineWrapping' => $this->formatBoolean('lineWrapping')]);
    $summary[] = $this->t('Line numbers: @lineNumbers', ['@lineNumbers' => $this->formatBoolean('lineNumbers')]);
    $summary[] = $this->t('Fold gutter: @foldGutter', ['@foldGutter' => $this->formatBoolean('foldGutter')]);
    $summary[] = $this->t('Auto close tags: @autoCloseTags', ['@autoCloseTags' => $this->formatBoolean('autoCloseTags')]);
    $summary[] = $this->t('Style active line: @styleActiveLine', ['@styleActiveLine' => $this->formatBoolean('styleActiveLine')]);
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $settings = $this->getSettings();

    $element['value'] = $element + [
      '#type' => 'codemirror',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
      '#rows' => $settings['rows'],
      '#placeholder' => $settings['placeholder'],
    ];

    // These options are not relevant to CodeMirror.
    unset($settings['rows'], $settings['placeholder']);
    $element['value']['#codemirror'] = $settings;

    $element['value']['#element_validate'] = [[get_class($this), 'validateTemplate']];

    $twig_field_name = $this->fieldDefinition->getName();
    $widget_html_id = $twig_field_name . '-' . $delta;
    $element['value']['#attributes']['data-tf-textarea'] = $widget_html_id;

    $element['footer'] = [
      '#type' => 'container',
      '#title' => $this->t('Twig context'),
      '#attributes' => ['class' => ['twig-field-editor-footer', 'container-inline']],
      '#weight' => 10,
    ];

    $options = ['' => $this->t('- Select -')];
    $default_context_names = array_keys(twig_field_default_context());
    $options['Global'] = array_combine($default_context_names, $default_context_names);

    $display_mode_id = $this->getFieldSetting('display_mode');
    $display_mode = EntityViewDisplay::load($display_mode_id);
    $components = $display_mode ? $display_mode->getComponents() : [];
    ksort($components);
    foreach ($components as $field_name => $component) {
      // Skip components that has not type property like 'Links' as we are not
      // supporting them.
      if ($twig_field_name != $field_name && isset($component['type'])) {
        $options['Fields'][$field_name] = $field_name;
      }
    }

    $entity_type = $this->fieldDefinition->getTargetEntityTypeId();
    $options['Other'][$entity_type] = $entity_type;

    $element['footer']['variables'] = [
      '#type' => 'select',
      '#title' => $this->t('Variables'),
      '#options' => $options,
      '#attributes' => ['data-tf-variables' => $widget_html_id],
    ];

    $element['footer']['insert'] = [
      '#type' => 'button',
      '#value' => $this->t('Insert'),
      '#attributes' => ['data-tf-insert' => $widget_html_id],
    ];

    $element['#attached']['library'][] = 'twig_field/editor';

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {

    // Footer is only used on client side. Unset it to avoid configuration
    // schema errors.
    foreach ($values as &$value) {
      unset($value['footer']);
    }

    return parent::massageFormValues($values, $form, $form_state);
  }

  /**
   * Validation callback for a Template element.
   */
  public static function validateTemplate(&$element, FormStateInterface $form_state) {
    $build = [
      '#type' => 'inline_template',
      '#template' => $element['#value'],
      '#context' => twig_field_default_context(),
    ];
    try {
      \Drupal::service('renderer')->renderPlain($build);
    }
    catch (\Exception $exception) {
      $form_state->setError($element, t('Template error: @error', ['@error' => $exception->getMessage()]));
    }
  }

}
