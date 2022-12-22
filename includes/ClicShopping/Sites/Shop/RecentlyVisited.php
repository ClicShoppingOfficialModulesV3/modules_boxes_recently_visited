<?php
  /**
   *
   * @copyright 2008 - https://www.clicshopping.org
   * @Brand : ClicShopping(Tm) at Inpi all right Reserved
   * @Licence GPL 2 & MIT
   * @licence MIT - Portion of osCommerce 2.4
   * @Info : https://www.clicshopping.org/forum/trademark/
   *
   */

  namespace ClicShopping\Sites\Shop;

  use ClicShopping\OM\Registry;
  use ClicShopping\OM\HTML;

  class RecentlyVisited
  {
    public $visits = [];
    protected $productsCommon;
    protected $prod;
    protected $customer;
    protected $db;
    protected $template;
    protected $productsFunctionTemplate;

    public function __construct()
    {
      if (!isset($_SESSION['Shop']['RecentlyVisited'])) {
        $_SESSION['Shop']['RecentlyVisited'] = [];
      }

      $this->visits =& $_SESSION['Shop']['RecentlyVisited'];

      $this->productsCommon = Registry::get('ProductsCommon');
      $this->productsFunctionTemplate = Registry::get('ProductsFunctionTemplate');

      $this->prod = Registry::get('Prod');
      $this->customer = Registry::get('Customer');
      $this->db = Registry::get('Db');
      $this->template = Registry::get('Template');

      if (defined('MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS') && MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS == 'True') {
        $this->setProduct($this->productsCommon->getId());
      }

      If (defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS') && MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS == 'True') {
        $this->setProduct($this->productsCommon->getId());
      }
    }

    public function setProduct($id)
    {
      if (isset($this->visits['products'])) {
        foreach ($this->visits['products'] as $key => $value) {
          if ($value['id'] == $id) {
            unset($this->visits['products'][$key]);
            break;
          }
        }

        if (defined('MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS')) {
          if (count($this->visits['products']) > (MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS * 2)) {
            array_pop($this->visits['products']);
          }
        }

        if (defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS')) {
          if (count($this->visits['products']) > (MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS * 2)) {
            array_pop($this->visits['products']);
          }
        }
      } else {
        $this->visits['products'] = [];
      }


      array_unshift($this->visits['products'], ['id' => $id]);
    }


    public function hasHistory()
    {
      if ($this->hasProducts()) {
        return true;
      }

      return false;
    }

    public function hasProducts()
    {
      if (defined('MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS') && MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCTS == 'True') {
        if (isset($this->visits['products']) && !empty($this->visits['products'])) {
          foreach ($this->visits['products'] as $k => $v) {
            if (!$this->productsCommon->checkEntry($v['id'])) {
              unset($this->visits['products'][$k]);
            }
          }

          return (count($this->visits['products']) > 0);
        }
      }

      if (defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS') && MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCTS == 'True') {
        if (isset($this->visits['products']) && !empty($this->visits['products'])) {
          foreach ($this->visits['products'] as $k => $v) {
            if (!$this->productsCommon->checkEntry($v['id'])) {
              unset($this->visits['products'][$k]);
            }
          }

          return (count($this->visits['products']) > 0);
        }
      }

      return false;
    }

    public function getProducts()
    {
      $history = [];

      if (isset($this->visits['products']) && (empty($this->visits['products']) === false)) {
        $counter = 0;

        foreach ($this->visits['products'] as $k => $v) {
          $counter++;

          $id = $this->prod->getProductID(($v['id']));

          if ($this->customer->getCustomersGroupID() != 0) {
            $Qproducts = $this->db->prepare('select p.products_id
                                              from :table_products p left join :table_products_groups g on p.products_id = g.products_id
                                              where p.products_status = 1
                                              and g.customers_group_id = :customers_group_id
                                              and g.products_group_view = 1
                                              and p.products_archive = 0
                                              and p.products_id = :products_id
                                           ');
            $Qproducts->bindInt(':customers_group_id', $this->customer->getCustomersGroupID());
            $Qproducts->bindInt(':products_id', $id);

            $Qproducts->execute();
          } else {
            $Qproducts = $this->db->prepare('select products_id
                                             from :table_products
                                              where products_status = 1
                                              and products_view = 1
                                              and products_archive = 0
                                              and products_id = :products_id
                                           ');

            $Qproducts->bindInt(':products_id', $id);
            $Qproducts->execute();
          }

          $price = '';

          if ($Qproducts->rowCount() > 0) {
            if (defined('MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES') && MODULE_BOXES_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True') {
              $price = $this->productsCommon->getCustomersPrice($id);
            } elseif (defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES') && MODULE_SHOPPING_CART_RECENTLY_VISITED_SHOW_PRODUCT_PRICES == 'True') {
              $price = $this->productsCommon->getCustomersPrice($id);
            }
          }

          $products_image = HTML::image($this->template->getDirectoryTemplateImages() . $this->productsCommon->getProductsImage($id), HTML::outputProtected($this->productsCommon->getProductsName($id)), 50, 50);

          $history[] = ['name' => $this->productsCommon->getProductsName($id),
            'id' => $id,
            'price' => $price,
            'image' => $products_image,
          ];

          if (defined('MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS')) {
            if ($counter == MODULE_BOXES_RECENTLY_VISITED_MAX_PRODUCTS) {
              break;
            }
          }

          if (defined('MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS')) {
            if ($counter == MODULE_SHOPPING_CART_RECENTLY_VISITED_MAX_PRODUCTS) {
              break;
            }
          }
        }
      }

      return $history;
    }
  }
