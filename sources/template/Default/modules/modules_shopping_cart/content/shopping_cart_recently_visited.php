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

use ClicShopping\OM\CLICSHOPPING;
?>
<div class="separator"></div>
<div class="separator"></div>
<section class="boxeContainerShoppingCartRecentlyVisited" id="boxeContainerShoppingCartRecentlyVisited">
  <div class="card-header boxeHeadingShoppingCartRecentlyVisited">
    <span class="card-title boxeTitleShoppingCartRecentlyVisited"><?php echo CLICSHOPPING::getDef('module_shopping_cat_recently_visited_box_title'); ?></span>
  </div>
  <div class="card-block boxeContentArroundShoppingCartRecentlyVisited">
    <div class="separator"></div>
    <?php echo $content; ?>
  </div>
</section>
