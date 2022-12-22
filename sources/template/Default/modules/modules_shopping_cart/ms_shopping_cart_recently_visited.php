<?php
/**
 *
 *  @copyright 2008 - https://www.clicshopping.org
 *  @Brand : ClicShopping(Tm) at Inpi all right Reserved
 *  @Licence GPL 2 & MIT
 *  @licence MIT - Portion of osCommerce 2.4
 *  @Info : https://www.clicshopping.org/forum/trademark/
 *
 */

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\CLICSHOPPING;
  use ClicShopping\OM\HTML;

  class ms_shopping_cart_recently_visited {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_recently_visited_recently_visited_title');
      $this->description = CLICSHOPPING::getDef('module_recently_visited_recently_visited_description');

      if ( defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_STATUS')) {
        $this->sort_order = MODULE_SHOPPING_CART_RECENTLY_VISITED_SORT_ORDER;
        $this->enabled = (MODULE_SHOPPING_CART_RECENTLY_VISITED_STATUS == 'True');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_RecentlyVisited = Registry::get('RecentlyVisited');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');
      $CLICSHOPPING_ShoppingCart = Registry::get('ShoppingCart');

      $CLICSHOPPING_Customer = Registry::get('Customer');

      if (isset($_GET['Cart']) && $CLICSHOPPING_ShoppingCart->getCountContents() > 0) {
        $count = 0;

        if ($CLICSHOPPING_Customer->isLoggedOn()) {
          if ( $CLICSHOPPING_Service->isStarted('RecentlyVisited') && $CLICSHOPPING_RecentlyVisited->hasHistory()) {
            $content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' .
              '  <tr>';

            $count = count($CLICSHOPPING_RecentlyVisited->getProducts());

            if ( $CLICSHOPPING_RecentlyVisited->hasProducts()) {
              $content .= '    <td valign="top">' .

                          '      <ol style="list-style: none; margin: 0; padding: 0;">';
              foreach ( $CLICSHOPPING_RecentlyVisited->getProducts() as $product ) {
                $content .= '<li style="padding-bottom: 15px;">';

                if ( MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == 'True' ) {
                  $content .= '<span class="text-center" style="width: 50px;">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['image']) . '</span>';
                }

                if ( MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True' ) {
                  $content .= '<span class="float-start modulesBoxesRecentlyVisitedShowProductsPrices">' . $product['price'] . '</span>&nbsp;';
                }

                $content .= '<span class="modulesBoxesRecentlyVisitedShowProductsName">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['name']) . '</span>';

                $content .= '<div class="clearfix"></div>';
                $content .= '</li>';
              }

              $content .= '      </ol>' .
                '    </td>';
            }

            $content .= '  </tr>' .
              '</table>';
          }
        } elseif (MODULE_SHOPPING_CART_RECENTLY_VISITED_LOGGED == 'False') {

          if ( $CLICSHOPPING_Service->isStarted('RecentlyVisited') && $CLICSHOPPING_RecentlyVisited->hasHistory()) {
            $count = count($CLICSHOPPING_RecentlyVisited->getProducts());

            $position = MODULE_SHOPPING_CART_RECENTLY_VISITED_POSITION;

            $content = '<div class="' . $position . '">';
            $content .= '<div class="d-flex flex-wrap">';

            if ( $CLICSHOPPING_RecentlyVisited->hasProducts()) {
              foreach ( $CLICSHOPPING_RecentlyVisited->getProducts() as $product ) {
                $content .= '<div class="col-md-' . MODULE_SHOPPING_CART_RECENTLY_VISITED_CONTENT_WIDTH .'">';

                if ( MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == 'True' ) {
                  $content .= '<div class="text-center">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['image']) . '</div>';
                }

                if ( MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True' ) {
                  $content .= '<div class="text-center modulesShoppingCartRecentlyVisitedShowProductsPrices">' . $product['price'] . '</div>';
                }

                $content .= '<div class="text-center modulesShoppingCartRecentlyVisitedShowProductsName">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['name']) . '</div>';
                $content .= '</div>';
              }
            }

            $content .= '</div>';
            $content .= '</div>';
          }
        }

        $data = '<!-- boxe recently start-->' . "\n";


        if ($count > 0) {

          ob_start();
          require_once($CLICSHOPPING_Template->getTemplateModules($this->group . '/content/shopping_cart_recently_visited'));
          $data .= ob_get_clean();

          $data .= '<!-- Boxe recently end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($data, $this->group);
        }
      }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Do you want to activate this module ?',
                                              'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_STATUS',
                                              'configuration_value' => 'True',
                                              'configuration_description' => 'Activate this module ?',
                                              'configuration_group_id' => '6',
                                              'sort_order' => '1',
                                              'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                              'date_added' => 'now()'
                                            ]
                      );

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Veuillez selectionner la largeur de l\'affichage?',
                                              'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_CONTENT_WIDTH',
                                              'configuration_value' => '4',
                                              'configuration_description' => 'Veuillez indiquer un nombre compris entre 1 et 12',
                                              'configuration_group_id' => '6',
                                              'sort_order' => '1',
                                              'set_function' => 'clic_cfg_set_content_module_width_pull_down',
                                              'date_added' => 'now()'
                                            ]
                      );

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Where Do you want to display the module ?',
                                              'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_POSITION',
                                              'configuration_value' => 'none',
                                              'configuration_description' => 'Select where you want display the module',
                                              'configuration_group_id' => '6',
                                              'sort_order' => '2',
                                              'set_function' => 'clic_cfg_set_boolean_value(array(\'float-end\', \'float-start\', \'float-none\'))',
                                              'date_added' => 'now()'
                                            ]
                    );

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Do you want to activate this module when the customer is logged ?',
                                              'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_LOGGED',
                                              'configuration_value' => 'True',
                                              'configuration_description' => 'Activate this module when the customer is logged',
                                              'configuration_group_id' => '6',
                                              'sort_order' => '3',
                                              'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                              'date_added' => 'now()'
                                              ]
                            );


      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'Do you want to display the products images ?',
                                        'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES',
                                        'configuration_value' => 'False',
                                        'configuration_description' => 'display the products images',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '1',
                                        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                        'date_added' => 'now()'
                                      ]
                      );


      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'Do you want to display the products ?',
                                        'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS',
                                        'configuration_value' => 'True',
                                        'configuration_description' => 'Display the products',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '1',
                                        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                        'date_added' => 'now()'
                                      ]
                      );

      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'How many products do your want to display ?',
                                        'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS',
                                        'configuration_value' => '5',
                                        'configuration_description' => 'Please, indicate a number',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '4',
                                        'set_function' => '',
                                        'date_added' => 'now()'
                                      ]
                      );

      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'Do you want to display the products price ?',
                                        'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES',
                                        'configuration_value' => 'False',
                                        'configuration_description' => 'display the products price',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '1',
                                        'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                        'date_added' => 'now()'
                                      ]
                      );


      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'Sort order',
                                        'configuration_key' => 'MODULE_SHOPPING_CART_RECENTLY_VISITED_SORT_ORDER',
                                        'configuration_value' => '120',
                                        'configuration_description' => 'Sort order of display. Lowest is displayed first',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '4',
                                        'set_function' => '',
                                        'date_added' => 'now()'
                                      ]
                      );

    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return ['MODULE_SHOPPING_CART_RECENTLY_VISITED_STATUS',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_CONTENT_WIDTH',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_POSITION',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_LOGGED',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES',
              'MODULE_SHOPPING_CART_RECENTLY_VISITED_SORT_ORDER',
             ];
    }
  }
