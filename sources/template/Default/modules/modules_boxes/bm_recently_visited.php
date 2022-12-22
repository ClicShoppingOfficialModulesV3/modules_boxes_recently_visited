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

  class bm_recently_visited {
    public $code;
    public $group;
    public $title;
    public $description;
    public $sort_order;
    public $enabled = false;
    public $pages;

    public function __construct() {
      $this->code = get_class($this);
      $this->group = basename(__DIR__);

      $this->title = CLICSHOPPING::getDef('module_boxes_recently_visited_title');
      $this->description = CLICSHOPPING::getDef('module_boxes_recently_visited_description');

      if ( defined('MODULE_BOXES_RECENTLY_VISITED_STATUS')) {
        $this->sort_order = MODULE_BOXES_RECENTLY_VISITED_SORT_ORDER;
        $this->enabled = (MODULE_BOXES_RECENTLY_VISITED_STATUS == 'True');
        $this->pages = MODULE_BOXES_RECENTLY_VISITED_DISPLAY_PAGES;
        $this->group = ((MODULE_BOXES_RECENTLY_VISITED_CONTENT_PLACEMENT == 'Left Column') ? 'boxes_column_left' : 'boxes_column_right');
      }
    }

    public function execute() {
      $CLICSHOPPING_Template = Registry::get('Template');
      $CLICSHOPPING_Service = Registry::get('Service');
      $CLICSHOPPING_Banner = Registry::get('Banner');
      $CLICSHOPPING_RecentlyVisited = Registry::get('RecentlyVisited');
      $CLICSHOPPING_ProductsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

      $CLICSHOPPING_Customer = Registry::get('Customer');

      $count = 0;

        if ($CLICSHOPPING_Customer->isLoggedOn() && MODULE_BOXES_RECENTLY_VISITED_LOGGED == 'True') {
          if ( $CLICSHOPPING_Service->isStarted('RecentlyVisited') && $CLICSHOPPING_RecentlyVisited->hasHistory()) {
            $content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' .
              '  <tr>';

            $count = count($CLICSHOPPING_RecentlyVisited->getProducts());

            if ( $CLICSHOPPING_RecentlyVisited->hasProducts()) {
              $content .= '    <td valign="top">' .
                          '      <ol style="list-style: none; margin: 0; padding: 0;">';
              foreach ( $CLICSHOPPING_RecentlyVisited->getProducts() as $product ) {
                $content .= '<li style="padding-bottom: 15px;">';

                if ( MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == 'True' ) {
                  $content .= '<span class="text-center" style="width: 50px;">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['image']) . '</span>';
                }

                if ( MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True' ) {
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
        } elseif (MODULE_BOXES_RECENTLY_VISITED_LOGGED == 'False') {
          if ( $CLICSHOPPING_Service->isStarted('RecentlyVisited') && $CLICSHOPPING_RecentlyVisited->hasHistory()) {
            if (is_array($CLICSHOPPING_RecentlyVisited->getProducts())) {
              $count = count($CLICSHOPPING_RecentlyVisited->getProducts());
            }

            $content = '<table border="0" width="100%" cellspacing="0" cellpadding="2">' .
              '  <tr>';

            if ( $CLICSHOPPING_RecentlyVisited->hasProducts()) {
              $content .= '    <td valign="top">' .

                '      <ol style="list-style: none; margin: 0; padding: 0;">';

              foreach ( $CLICSHOPPING_RecentlyVisited->getProducts() as $product ) {
                $content .= '<li style="padding-bottom: 15px;">';

                if ( MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES == 'True' ) {
                  $content .= '<span class="text-center" style="width: 50px;">' . HTML::link($CLICSHOPPING_ProductsFunctionTemplate->getProductsUrlRewrited()->getProductNameUrl($product['id']), $product['image']) . '</span>';
                }

                if ( MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True' ) {
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
        }

        $logo = '';
	  
        if ($CLICSHOPPING_Service->isStarted('Banner')) {
          if ($banner =  $CLICSHOPPING_Banner->bannerExists('dynamic', MODULE_BOXES_RECENTLY_VISITED_BANNER_GROUP)) {
            $logo = $CLICSHOPPING_Banner->displayBanner('static', $banner) . '<br /><br />';
          } else {
            $logo = '';
          }
        }

        $data = '<!-- boxe recently start-->' . "\n";

        if ($count > 0) {
          ob_start();
          require($CLICSHOPPING_Template->getTemplateModules('/modules_boxes/content/recently_visited'));
          $data .= ob_get_clean();

          $data .= '<!-- Boxe recently end -->' . "\n";

          $CLICSHOPPING_Template->addBlock($data, $this->group);
        }
    }

    public function isEnabled() {
      return $this->enabled;
    }

    public function check() {
      return defined('MODULE_BOXES_RECENTLY_VISITED_STATUS');
    }

    public function install() {
      $CLICSHOPPING_Db = Registry::get('Db');

      $CLICSHOPPING_Db->save('configuration', [
                                          'configuration_title' => 'Do you want to activate this module ?',
                                          'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_STATUS',
                                          'configuration_value' => 'True',
                                          'configuration_description' => 'Activate this module ?',
                                          'configuration_group_id' => '6',
                                          'sort_order' => '1',
                                          'set_function' => 'clic_cfg_set_boolean_value(array(\'True\', \'False\'))',
                                          'date_added' => 'now()'
                                        ]
                            );

      $CLICSHOPPING_Db->save('configuration', [
                                        'configuration_title' => 'Please choose where the boxe must be displayed',
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_CONTENT_PLACEMENT',
                                        'configuration_value' => 'Right Column',
                                        'configuration_description' => 'Choose where the boxe must be displayed',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '2',
                                        'set_function' => 'clic_cfg_set_boolean_value(array(\'Left Column\', \'Right Column\'))',
                                        'date_added' => 'now()'
                                      ]
                            );

      $CLICSHOPPING_Db->save('configuration', [
                                          'configuration_title' => 'Please indicate the banner group for the image',
                                          'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_BANNER_GROUP',
                                          'configuration_value' => SITE_THEMA . '_boxe_recently_visited',
                                          'configuration_description' => 'Indicate the banner group<br /><br /><strong>Note :</strong><br /><i>The group must be created or selected whtn you create a banner in Marketing / banner</i>',
                                          'configuration_group_id' => '6',
                                          'sort_order' => '3',
                                          'set_function' => '',
                                          'date_added' => 'now()'
                                        ]
                            );

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Do you want to activate this module when the customer is logged ?',
                                              'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_LOGGED',
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
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES',
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
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS',
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
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS',
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
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES',
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
                                        'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_SORT_ORDER',
                                        'configuration_value' => '120',
                                        'configuration_description' => 'Sort order of display. Lowest is displayed first',
                                        'configuration_group_id' => '6',
                                        'sort_order' => '4',
                                        'set_function' => '',
                                        'date_added' => 'now()'
                                      ]
                      );

      $CLICSHOPPING_Db->save('configuration', [
                                              'configuration_title' => 'Indicate the page where the module is displayed',
                                              'configuration_key' => 'MODULE_BOXES_RECENTLY_VISITED_DISPLAY_PAGES',
                                              'configuration_value' => 'all',
                                              'configuration_description' => 'Select the page where the modules must be displayed',
                                              'configuration_group_id' => '6',
                                              'sort_order' => '5',
                                              'set_function' => 'clic_cfg_set_select_pages_list',
                                              'date_added' => 'now()'
                                              ]
                            );
    }

    public function remove() {
      return Registry::get('Db')->exec('delete from :table_configuration where configuration_key in ("' . implode('", "', $this->keys()) . '")');
    }

    public function keys() {
      return ['MODULE_BOXES_RECENTLY_VISITED_STATUS',
              'MODULE_BOXES_RECENTLY_VISITED_CONTENT_PLACEMENT',
              'MODULE_BOXES_RECENTLY_VISITED_BANNER_GROUP',
              'MODULE_BOXES_RECENTLY_VISITED_LOGGED',
              'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_IMAGES',
              'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS',
              'MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS',
              'MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES',
              'MODULE_BOXES_RECENTLY_VISITED_SORT_ORDER',
              'MODULE_BOXES_RECENTLY_VISITED_DISPLAY_PAGES'
             ];
    }
  }
