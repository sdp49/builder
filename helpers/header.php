<?php

PL_Helper_Header::init();

class PL_Helper_Header {
    
    private static $page;

    public static function init () {
        add_action('admin_enqueue_scripts', array(__CLASS__, 'set_page' ) );
    }

    public static function set_page ($hook) {
        self::$page = $hook;
    }

    public static function pl_settings_subpages () {
        global $settings_subpages;
        global $submenu;
        
        $base_page = 'placester_settings';
        $base_url = 'admin.php?page='.$base_page;
        
        ob_start();
        ?>
          <div class="settings_sub_nav">
            <ul>
              <li class="submenu-title">Settings Pages:</li>
              <?php foreach ($settings_subpages as $page_title => $page_url): ?>
                <li>
                  <?php
                    $current_page = ( !empty($page_url) ? strpos(self::$page, $page_url) : ($_REQUEST['page'] == $base_page) );
                  ?>
                  <a href="<?php echo $base_url . $page_url ?>" style="<?php echo $current_page ? 'color:#D54E21;' : '' ?>"><?php echo $page_title; ?></a>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php
        
        return ob_get_clean();
    }

    public static function placester_admin_header($title_postfix = '' ) {
        // placester_verified_check()
        global $wp_rewrite;
        global $submenu;

        $current_title = '';
        $menu = '';

        foreach ($submenu['placester'] as $i) {
            // Exclude settings submenu pages
            $check = explode('_', $i[2]);
            if (count($check) > 2 && $check[1] == 'settings') { continue; }
            
            $title = $i[0];
            $slug = $i[2];
            $style = '';

            if (strpos(self::$page, $slug)) {
                $style = 'nav-tab-active';
                $current_title = $title;
            }

            $id = str_replace(' ', '_', $title);
            
            // Handle custom links for listings
            if( strpos( $slug, 'edit.php?' ) === false ) {
                $menu .= "<a href='admin.php?page=$slug' style='font-size: 15px' class='nav-tab $style' id='$id'>$title</a>";
            } 
            else {
                $menu .= "<a href='$slug' style='font-size: 15px' class='nav-tab $style' id='$id'>$title</a>";
            }  
        }

        // Render header menu...
        ?>
            <div class='clear'></div>
            <!-- <div id="icon-options-general" class="icon32 placester_icon"><br /></div> -->
            <h2 id="placester-admin-menu">
            <?php 
                echo $current_title; 
                echo $title_postfix; 
                echo "&nbsp;&nbsp;&nbsp;";
                echo $menu; 
            ?>
            </h2>
        <?php
    }

}

?>