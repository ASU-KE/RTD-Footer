<?php

namespace Drupal\rtd_footer\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\file\Entity\File;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\system\Entity\Menu;

/**
 * Provides a 'RtdFooterBlock' block.
 *
 * @Block(
 *  id = "rtd_footer",
 *  admin_label = @Translation("RTD Footer"),
 * )
 */
class RtdFooterBlock extends BlockBase implements BlockPluginInterface
{
    /**
     * {@inheritdoc}
     *
     * This method sets the block default configuration. This configuration
     * determines the block's behavior when a block is initially placed in a
     * region. Default values for the block configuration form should be added to
     * the configuration array. System default configurations are assembled in
     * BlockBase::__construct() e.g. cache setting and block title visibility.
     *
     * @see \Drupal\block\BlockBase::__construct()
     *
     *
     * /**
     * {@inheritdoc}
     */
    public function defaultConfiguration()
    {
        return [
                'brand_logo' => [
                    'value' => '',
                ],
                'address' => [
                    'value' => '',
                    'format' => 'full_html'
                ],
            ] + parent::defaultConfiguration();
    }

  /**
   * Pass a menu name and get a list of menu links.
   *
   * @param string $menu_name Menu machine name.
   * @return array Associative array of menu items.
   */
  protected function getMenuItems($menu_name) {

    $menu = [];

    $menu_tree = \Drupal::menuTree();

    // Build the typical default set of menu tree parameters.
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(3);

    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);

    // Transform the tree using the manipulators you want.
    $manipulators = [
      // Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);

    // Finally, build a renderable array from the transformed tree.
    $menu_tmp = $menu_tree->build($tree);

    foreach ($menu_tmp['#items'] as $item) {
      $top_level = $this->getMenuItem($item);
      if (!empty($item['below'])) {
        foreach ($item['below'] as $child) {
          $second_level = $this->getMenuItem($child);
          if (!empty($child['below'])) {
            foreach ($child['below'] as $grandchild) {
              $second_level['children'][] = $this->getMenuItem($grandchild);
            }
          }
          $top_level['children'][] = $second_level;
        }
      }
      $menu[] = $top_level;
    }

    return $menu;
  }

  /**
   * Compose and return menu item
   *
   * @param array $item
   * @return array $menu_item
   */
  protected function getMenuItem($item) {

    return [
      'title' => $item['title'],
      'path' => $item['url']->toString(),
    ];

  }

  /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state)
    {
        $form = parent::blockForm($form, $form_state);
        $address = $this->configuration['address'];
        $phone = $this->configuration['phone'];
        $brand_logo = $this->configuration['brand_logo'];
        $facebook = $this->configuration['social_media']['facebook'];
        $twitter = $this->configuration['social_media']['twitter'];
        $instagram = $this->configuration['social_media']['instagram'];
        $middle_column = $this->configuration['middle_column'];
        $menu = $this->configuration['menu'];

        $validators = array(
            'file_validate_is_image' => array(),
            'file_validate_extensions' => array('gif png jpg jpeg'),
            'file_validate_size' => array(25600000)
        );

        $form['brand_logo'] = [
            '#type' => 'managed_file',
            '#name' => 'brand_logo',
            '#title' => t('Brand Logo'),
            '#size' => 20,
            '#multiple' => FALSE,
            '#description' => t('Allowed images: gif, png, jpg, jpeg. Recommended dimension: 360px x 72px.'),
            '#upload_validators' => $validators,
            '#upload_location' => 'public://brand_logo/',
            '#default_value' => $brand_logo,
        ];

        $form['address'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Address'),
            '#description' => $this->t('Add custom address.'),
            '#default_value' => $address['value'],
            '#format' => $address['format'],
        ];

        $form['phone'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Phone'),
            '#description' => $this->t('Add phone number.'),
            '#default_value' => $phone,
        ];

        $form['social_media'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Social media links'),
            '#description' => $this->t('Add social media.'),
        ];

        $form['social_media']['facebook'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Facebook link'),
            '#default_value' => $facebook,
        ];

        $form['social_media']['twitter'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Twitter link'),
            '#default_value' => $twitter,
        ];

        $form['social_media']['instagram'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Instagram link'),
            '#default_value' => $instagram,
        ];

        $form['middle_column'] = [
            '#type' => 'text_format',
            '#title' => $this->t('Middle column content'),
            '#description' => $this->t('Add custom content for middle column content.'),
            '#format' => $address['format'],
            '#default_value' => $middle_column['value'],
        ];

        $form['menu'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Add first-level menu to footer?'),
            '#description' => $this->t('Check to add primary level menu to footer.'),
            '#default_value' => $menu,
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state)
    {
        parent::blockSubmit($form, $form_state);
        $brand_logo = $form_state->getValue('brand_logo');
        $file = File::load ($brand_logo[0]);
        $file->setPermanent();
        $file->save();

        $this->configuration['brand_logo'] = $brand_logo;
        $this->configuration['address'] = $form_state->getValue('address');
        $this->configuration['phone'] = $form_state->getValue('phone');
        $this->configuration['middle_column'] = $form_state->getValue('middle_column');
        $this->configuration['menu'] = $form_state->getValue('menu');
        $this->configuration['social_media']['facebook'] = $form_state->getValue(['social_media','facebook']);
        $this->configuration['social_media']['twitter'] = $form_state->getValue(['social_media', 'twitter']);
        $this->configuration['social_media']['instagram'] = $form_state->getValue(['social_media', 'instagram']);

    }

  /**
     * {@inheritdoc}
     */
    public function build()
    {
        $config = $this->getConfiguration();

        $build = [];

        $image_field = $this->configuration['brand_logo'];
        $image_uri = File::load($image_field[0]);
/*        $menu_items_array = menu_list_system_menus();*/
        $menu_name = 'main';

        $build = [
            '#theme' => 'rtd_footer',
            '#address' => $config['address']['value'],
            '#phone' => $config['phone'],
            '#brand_logo' =>  [
                '#theme' => 'image_style',
                '#style_name' => 'thumbnail',
                '#uri' => $image_uri->uri->value
            ],
            '#facebook' => $config['social_media']['facebook'],
            '#twitter' => $config['social_media']['twitter'],
            '#instagram' => $config['social_media']['instagram'],
            '#menu' => $config['menu'],
            '#middle_column' => $config['middle_column']['value'],
            '#menu_items_var' => $this->getMenuItems($menu_name),
        ];

        return $build;
    }
}