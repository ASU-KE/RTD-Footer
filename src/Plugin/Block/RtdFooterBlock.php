<?php

namespace Drupal\rtd_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
//use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'RtdFooterBlock' block.
 *
 * @Block(
 *  id = "rtd_footer_block",
 *  admin_label = @Translation("Rtd footer block"),
 * )
 */
class RtdFooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
/*  public function defaultConfiguration() {
    return [
      'logo' => $this->t(''),
      'address' => $this->t(''),
    ] + parent::defaultConfiguration();
  }*/

  /**
   * {@inheritdoc}
   */
 /* public function blockForm($form, FormStateInterface $form_state) {
    $form['logo'] = [
      '#type' => 'file',
      '#title' => $this->t('Logo'),
      '#description' => $this->t('Upload image file of client&#039;s logo'),
      '#default_value' => $this->configuration['logo'],
      '#weight' => '0',
    ];
    $form['address'] = [
      '#type' => 'text_format',
      '#title' => $this->t('address'),
      '#description' => $this->t('Add custom address.'),
      '#default_value' => $this->configuration['address'],
      '#weight' => '0',
    ];

    return $form;
  }*/

  /**
   * {@inheritdoc}
   */
/*  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['logo'] = $form_state->getValue('logo');
    $this->configuration['address'] = $form_state->getValue('address');
  }*/

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    //$build['rtd_footer_block_logo']['#markup'] = '<p>' . $this->configuration['logo'] . '</p>';
    //$build['rtd_footer_block_address']['#markup'] = '<p>' . $this->configuration['address'] . '</p>';
      $build['rtd_footer'] = [
          '#theme' => 'rtd_footer',
          '#attached' => ['library' => 'rtd_footer/footer'],
      ];

      return $build;
  }

}
