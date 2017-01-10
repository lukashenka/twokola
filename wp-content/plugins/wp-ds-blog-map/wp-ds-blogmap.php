<?php
/*
Plugin Name: WP DS Blog Map
Plugin URI: http://wp-plugins.diamondsteel.ru/
Description: This plugin generates Blog Map. / Этот плагин предназначен для создания карты сайта/блога.
Version: 3.1.3
Author: DiamondSteel
Author URI: http://diamondsteel.ru
*/

class dsblogmap{
    var $plugurl;
    var $wpdsblogmap_settings_default;

    ##############################################################
    # dsblogmap()                                                #
    #   Конструктор                                              #
    ##############################################################------------------------------------------------------------#
    function dsblogmap(){
        $this->plugurl = WP_PLUGIN_URL.'/'.str_replace(basename(__FILE__),"",plugin_basename(__FILE__));

        $this->wpdsblogmap_settings_default['ver']               = '313';
        $this->wpdsblogmap_settings_default['prefix']            = '';
        $this->wpdsblogmap_settings_default['tags_rep_str']      = '[tagcloud]';
        $this->wpdsblogmap_settings_default['posts_rep_str']     = '[postlist]';
        $this->wpdsblogmap_settings_default['pages_rep_str']     = '[pagesoftree]';
        $this->wpdsblogmap_settings_default['expand_text']       = 'Show&nbsp;all&nbsp;&rarr;';
        $this->wpdsblogmap_settings_default['tags_limit']        = 0;
        $this->wpdsblogmap_settings_default['posts_limit']       = 0;
        $this->wpdsblogmap_settings_default['page_dept']         = 0;
        $this->wpdsblogmap_settings_default['posts_description'] = true;
        $this->wpdsblogmap_settings_default['hidden_categories'] = array(0 => '98t4irubfuga76ert2ou3rbpiut8yp4kgjfn87ty349ith3');
        
        add_action('init',       array(&$this, 'enable_getext'));
        add_action('wp_head',    array(&$this,'add_to_wp_head'));
        add_action('admin_menu', array(&$this, 'add_to_settings_menu'));

        add_filter('the_content', array(&$this, 'wp_ds_blogmap_filter'));

        register_activation_hook(__FILE__,   array(&$this, 'installer'));
        register_deactivation_hook(__FILE__, array(&$this, 'deactivate'));
        register_uninstall_hook(__FILE__, array(&$this, 'uninstall'));
    }
    # END dsfaq ##################################################------------------------------------------------------------#

    ##############################################################
    # enable_getext()                                            #
    #                                                            #
    # Говорим WordPress-у что у нас многоязычие в плагине        #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function enable_getext() {
        if (function_exists('load_plugin_textdomain')) {
            load_plugin_textdomain('wp-ds-blogmap', '/'.str_replace(ABSPATH, '', dirname(__FILE__)));
        }
    }
    # END enable_getext ##########################################------------------------------------------------------------#    

    ##############################################################
    # add_to_wp_head()                                           #
    #                                                            #
    # Предназначена для вывода произвольных строк в разделе HEAD #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function add_to_wp_head(){
        $settings = get_option('wp_ds_blogmap_array');
    
        // use JavaScript SACK library for Ajax
        wp_print_scripts( array( 'sack' ));
?>
        <!-- WP DS Blog Map Function -->
        <link rel="stylesheet" href="<?php echo $this->plugurl; ?>wp-ds-blogmap.css" type="text/css" media="screen" />
        <?php if((int)$settings['tags_limit'] !== 0 or (int)$settings['posts_limit'] !== 0){ ?>
            <script type="text/javascript">
                //<![CDATA[
                <?php if((int)$settings['tags_limit'] !== 0){ ?>
                    function pl_showcloud(){
                        var mysack = new sack("<?php echo $this->plugurl; ?>ajax.php" );
                        mysack.execute = 1;
                        mysack.method = 'POST';
                        mysack.setVar( 'action', 'show_cloud' );
                        mysack.setVar( 'id', '' );
                        mysack.onError = function() { alert('Ajax error.' )};
                        mysack.runAJAX();
                        return true;
                    }
                <?php } ?>
                <?php if((int)$settings['posts_limit'] !== 0){ ?>
                    function pl_showcat( cat_id ){
                        var mysack = new sack("<?php echo $this->plugurl; ?>ajax.php" );
                        mysack.execute = 1;
                        mysack.method = 'POST';
                        mysack.setVar( 'action', 'show_cat' );
                        mysack.setVar( 'id', cat_id );
                        mysack.onError = function() { alert('Ajax error.' )};
                        mysack.runAJAX();
                        return true;
                    }
                <?php } ?>
                //]]>
            </script>
        <?php } ?>
        <!-- END WP DS Blog Map Function -->
<?php
    }
    # END add_to_wp_head #########################################------------------------------------------------------------#
    
    ##############################################################
    # add_to_settings_menu                                       #
    #                                                            #
    # Предназначена для добавления настроек плагина              #
    #     в меню администрирования                               #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function add_to_settings_menu(){
        add_submenu_page('options-general.php', 'Sitemap Settings', 'DS Blog Map', 10, __FILE__, array(&$this, 'wp_ds_blogmap_options'));
    }
    # END add_to_settings_menu ###################################------------------------------------------------------------#

    ##############################################################
    # installer()                                                #
    #                                                            #
    # Функция выполняемая при активации плагина                  #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function installer() {
        if(!get_option('wp_ds_blogmap_array')){
            add_option('wp_ds_blogmap_array', $this->wpdsblogmap_settings_default);
        }
        
        $settings = get_option('wp_ds_blogmap_array');
        if(!isset($settings['ver'])){
            $settings['ver'] = 300;
            $settings['posts_description'] = true;
            update_option('wp_ds_blogmap_array', $settings);
        }
        
        $settings = get_option('wp_ds_blogmap_array');
        if($settings['ver'] < 312 and !is_array($settings['hidden_categories'])){
            $settings['ver'] = 312;
            $settings['hidden_categories'] = array(0 => '98t4irubfuga76ert2ou3rbpiut8yp4kgjfn87ty349ith3');
            update_option('wp_ds_blogmap_array', $settings);
        }
        
        $settings = get_option('wp_ds_blogmap_array');
        if($settings['ver'] < 313){
            $settings['ver'] = 313;
            update_option('wp_ds_blogmap_array', $settings);
        }
    }
    # END installer ##############################################------------------------------------------------------------#
    
    ##############################################################
    # deactivate()                                               #
    #   Функция вызываемая при деактивации плагина               #
    ##############################################################------------------------------------------------------------#
    function deactivate(){
        return true;
    }
    # END deactivate #############################################------------------------------------------------------------#
    
    ##############################################################
    # uninstall()                                                #
    #   Функция вызываемая при удалении плагина                  #
    ##############################################################------------------------------------------------------------#
    function uninstall(){
        delete_option('wp_ds_blogmap_array');
    }
    # END uninstall ##############################################------------------------------------------------------------#
    
    ##############################################################
    # wp_ds_blogmap_filter                                       #
    #                                                            #
    # Предназначена для замены в контенте подстрок указанных в   #
    #    настройках на соответсвующие блоки данных               #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_filter($content){
        $settings = get_option('wp_ds_blogmap_array');

        $tags_rep_str  = $settings['tags_rep_str'];
        $posts_rep_str = $settings['posts_rep_str'];
        $pages_rep_str = $settings['pages_rep_str'];
        $tags_limit    = $settings['tags_limit'];

        if(strlen($tags_rep_str) && strpos($content, $tags_rep_str) !== false) {
            $tags = '<div class="pl_cloud">'.'<div id="pl_cloud">'.$this->wp_ds_blogmap_cloud(($tags_limit === 0)?false:true).'</div></div>';
            $content = str_replace($tags_rep_str, $tags, $content);
        }
        if(strlen($posts_rep_str) && strpos($content, $posts_rep_str) !== false) {
            $posts = $this->wp_ds_blogmap_posts();
            $content = str_replace($posts_rep_str, $posts, $content);
        }
        if(strlen($pages_rep_str) && strpos($content, $pages_rep_str) !== false) {
            $pages = $this->wp_ds_blogmap_pages();
            $content = str_replace($pages_rep_str, $pages, $content);
        }
        return $content;
    }
    # END wp_ds_blogmap_filter ###################################------------------------------------------------------------#
    
    ##############################################################
    # wp_ds_blogmap_update_settings()                            #
    #                                                            #
    # Предназначена для сохранения настроек плагина в            #
    #     базу данных WordPress                                  #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_update_settings($settings = false){
        update_option('wp_ds_blogmap_array', ($settings)?$settings:$this->wpdsblogmap_settings_default);
        echo '<div id="message" class="updated fade"><p><strong>'.__('WP DS Blog Map plugin options updated.', 'wp-ds-blogmap').'</strong></p></div>';
    }
    # END wp_ds_blogmap_update_settings ##########################------------------------------------------------------------#
    
    ##############################################################
    # wp_ds_blogmap_options()                                    #
    #                                                            #
    # Предназначена для управления настройками плагина           #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_options(){
        if(isset($_POST['submitted'])){
            
            if(strlen($_POST['prefix']) > 255) { $_POST['prefix'] = substr($_POST['prefix'], 0, 255); }
            elseif($_POST['prefix'] == '') {$_POST['prefix'] = $this->wpdsblogmap_settings_default['prefix']; }
		
            if(strlen($_POST['tags_rep_str']) > 255) { $_POST['tags_rep_str'] = substr($_POST['tags_rep_str'], 0, 255);}
            elseif($_POST['tags_rep_str'] == '') { $_POST['tags_rep_str'] = $this->wpdsblogmap_settings_default['tags_rep_str']; }
		
            if(strlen($_POST['posts_rep_str']) > 255) { $_POST['posts_rep_str'] = substr($_POST['posts_rep_str'], 0, 255); }
            elseif($_POST['posts_rep_str'] == '') { $_POST['posts_rep_str'] = $this->wpdsblogmap_settings_default['posts_rep_str']; }
		
            if(strlen($_POST['pages_rep_str']) > 255) { $_POST['pages_rep_str'] = substr($_POST['pages_rep_str'], 0, 255); }
            elseif($_POST['pages_rep_str'] == '') { $_POST['pages_rep_str'] = $this->wpdsblogmap_settings_default['pages_rep_str']; }

            if(is_numeric($_POST['page_dept']) && ($_POST['page_dept'] > 0)) { $_POST['page_dept'] = (int)$_POST['page_dept']; }
            else { $_POST['page_dept'] = $this->wpdsblogmap_settings_default['page_dept']; }

    		if(strlen($_POST['expand_text']) > 255) { $_POST['expand_text'] = substr($_POST['expand_text'], 0, 255); }
            elseif($_POST['expand_text'] == '') { $_POST['expand_text'] = $this->wpdsblogmap_settings_default['expand_text']; }
		
            if(is_numeric($_POST['tags_limit']) && ($_POST['tags_limit'] > 0)) { $_POST['tags_limit'] = (int)$_POST['tags_limit']; }
            else { $_POST['tags_limit'] = $this->wpdsblogmap_settings_default['tags_limit']; }
		
            if(is_numeric($_POST['posts_limit']) && ($_POST['posts_limit'] > 0)) { $_POST['posts_limit'] = (int)$_POST['posts_limit']; }
            elseif(!is_numeric($_POST['posts_limit'])) { $_POST['posts_limit'] = $this->wpdsblogmap_settings_default['posts_limit']; }
            
            if($_POST['posts_description'] == 'true' or $_POST['posts_description'] == 'on'){ $posts_description = true; } else { $posts_description = false; }
            
            if(is_array($_POST['hidden_categories'])) { $hidden_categories = $_POST['hidden_categories']; }
            elseif(empty($_POST['hidden_categories'])) { $_POST['hidden_categories'] = $this->wpdsblogmap_settings_default['hidden_categories']; }
		
            $settings = array (
                'prefix' => $_POST['prefix'],
                'tags_rep_str' => $_POST['tags_rep_str'],
                'posts_rep_str' => $_POST['posts_rep_str'],
                'expand_text' => $_POST['expand_text'],
                'tags_limit' => $_POST['tags_limit'],
                'posts_limit' => $_POST['posts_limit'],
                'pages_rep_str' => $_POST['pages_rep_str'],
                'page_dept' => $_POST['page_dept'],
                'posts_description' => $posts_description,
                'hidden_categories' => $hidden_categories
            );
		
            $this->wp_ds_blogmap_update_settings($settings);
        }
	
        if(isset($_POST['wp_ds_blogmap_remove'])) { $this->wp_ds_blogmap_update_settings(); }

        $settings = get_option('wp_ds_blogmap_array');
    
        $prefix = stripcslashes($settings['prefix']);
        $tags_rep_str = stripcslashes($settings['tags_rep_str']);
        $posts_rep_str = stripcslashes($settings['posts_rep_str']);
        $pages_rep_str = stripcslashes($settings['pages_rep_str']);
        $page_dept = $settings['page_dept'];
        $expand_text = $settings['expand_text'];
        $tags_limit = $settings['tags_limit'];
        $posts_limit = $settings['posts_limit'];
        $posts_description = $settings['posts_description'];
        $hidden_categories = $settings['hidden_categories'];
        
        
        
        // Статистика
        $num_posts   = wp_count_posts( 'post' );
        $count_posts = $num_posts->publish; //publish, draft
        
        $num_pages   = wp_count_posts( 'page' );
        $count_pages = $num_pages->publish; //publish
        
        $count_categories  = wp_count_terms('category');
        
        $count_tags  = wp_count_terms('post_tag');
?>

        <div class="wrap">
            <H2><?php _e('WP DS Blog Map options', 'wp-ds-blogmap'); ?></H2>
            <BR />
	        <div class="metabox-holder has-right-sidebar">
    	        <div class="inner-sidebar">
        	        <div class="postbox">
            	        <h3><span><?php _e('Statistics', 'wp-ds-blogmap'); ?></span></h3>
            	        <div class="inside">
                	        <p><?php _e('Total posts:', 'wp-ds-blogmap'); ?> <b><?php echo $count_posts; ?></b></p>
                	        <p><?php _e('Total pages:', 'wp-ds-blogmap'); ?> <b><?php echo $count_pages; ?></b></p>
                	        <p><?php _e('Total categories:', 'wp-ds-blogmap'); ?> <b><?php echo $count_categories; ?></b></p>
                	        <p><?php _e('Total tags:', 'wp-ds-blogmap'); ?> <b><?php echo $count_tags; ?></b></p>
                    	</div>
	                </div>

	                <div class="postbox">
            	        <h3><span><?php _e('Donate', 'wp-ds-blogmap'); ?></span></h3>
            	        <div class="inside">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top" style="text-align: center;">
                                <input type="hidden" name="cmd" value="_s-xclick">
                                <input type="hidden" name="hosted_button_id" value="XVCYV4N8MATBC">
                                <input type="image" src="<?php echo $this->plugurl; ?>icon-donate.gif" border="0" name="submit" alt="PayPal">
                                <img alt="" border="0" src="https://www.paypalobjects.com/ru_RU/i/scr/pixel.gif" width="1" height="1">
                                <BR />
                                <?php _e('Thanks for your support!', 'wp-ds-blogmap'); ?>
                            </form>
                    	</div>
	                </div>
    	        </div>
                
        	    <div id="post-body">
            	    <div id="post-body-content">
                	    <div class="postbox">
                    	    <h3><span><?php _e('Options', 'wp-ds-blogmap'); ?></span></h3>
                        	<div class="inside">
                            <form method="post" name="options" target="_self">
                                <table class="form-table">
                                    <tr valign="top">
                                        <th scope="row"><?php _e('This string in the page content will be replaced by the tag cloud', 'wp-ds-blogmap'); ?></th>
                                        <td><input name="tags_rep_str" type="text" style="width:28em;" value="<?php echo $tags_rep_str; ?>" /><br /><span style="color:gray;"><?php _e('(Default value is: <code>[tagcloud]</code>)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row" style="border-bottom: 1px solid #CCCCCC;"><?php _e('Max tags number', 'wp-ds-blogmap'); ?></th>
                                        <td style="border-bottom: 1px solid #CCCCCC;"><input name="tags_limit" type="text" style="width:10em;" value="<?php echo str_replace('"', '\"', $tags_limit); ?>" /><br /><span style="color:gray;"><?php _e('(Default value is: 0 - tags unlimited)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>

                                    <tr valign="top">
                                        <th scope="row"><?php _e('This string will be replaced by the posts list', 'wp-ds-blogmap'); ?></th>
                                        <td><input name="posts_rep_str" type="text" style="width:28em;" value="<?php echo $posts_rep_str; ?>" /><br /><span style="color:gray;"><?php _e('(Default value is: <code>[postlist]</code>)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><?php _e('Category prefix', 'wp-ds-blogmap'); ?></th>
                                        <td><input name="prefix" type="text" style="width:28em;" value="<?php echo str_replace('"', '\"', $prefix); ?>" /><br /><span style="color:gray;"><?php _e('(If You will want to add some text before name of every categories. By default - emptily.)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row"><?php _e('Max posts number', 'wp-ds-blogmap'); ?></th>
                                        <td><input name="posts_limit" type="text" style="width:10em;" value="<?php echo str_replace('"', '\"', $posts_limit); ?>" /><br /><span style="color:gray;"><?php _e('(Default value is: 0 - posts unlimited)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row" style="border-bottom: 1px solid #CCCCCC;"><?php _e('View categories\' description:', 'wp-ds-blogmap'); ?></th>
                                        <td style="border-bottom: 1px solid #CCCCCC;"><input name="posts_description" type="checkbox" <?php if ($posts_description == true){echo " checked";}; ?> /></td>
                                    </tr>

                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>

                                    <tr valign="top">
                                        <th scope="row"><?php _e('Text, which will is replaced to tree of the pages', 'wp-ds-blogmap'); ?></th>
                                        <td><input name="pages_rep_str" type="text" style="width:28em;" value="<?php echo $pages_rep_str; ?>" /><br /><span style="color:gray;"><?php _e('(Default value is: <code>[pagesoftree]</code>)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr valign="top">
                                        <th scope="row" style="border-bottom: 1px solid #CCCCCC;"><?php _e('Level to nesting of the pages', 'wp-ds-blogmap'); ?></th>
                                        <td style="border-bottom: 1px solid #CCCCCC;"><input name="page_dept" type="text" style="width:28em;" value="<?php echo $page_dept; ?>" /><br /><span style="color:gray;"><?php _e('(&laquo;0&raquo; - all pages in the manner of hierarchical tree;  &laquo;1&raquo; - only pages of the top-level to hierarchies; &laquo;2&raquo; and more - specified level to nesting.)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>

                                    <tr valign="top">
                                        <th scope="row" style="border-bottom: 1px solid #CCCCCC;"><?php _e('Expand link text', 'wp-ds-blogmap'); ?></th>
                                        <td style="border-bottom: 1px solid #CCCCCC;"><input name="expand_text" type="text" style="width:28em;" value="<?php echo str_replace('"', '\"', $expand_text); ?>" /><br /><span style="color:gray;"><?php _e('(Default value is "Show all&nbsp;&rarr;")', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                    <tr><td>&nbsp;</td><td>&nbsp;</td></tr>

                                    <tr valign="top">
                                        <th scope="row" style="border-bottom: 1px solid #CCCCCC;"><?php _e('Category that should be excluded', 'wp-ds-blogmap'); ?></th>
                                        <td style="border-bottom: 1px solid #CCCCCC;"><?php echo $this->wp_ds_blogmap_cat_checkbox($hidden_categories);?><span style="color:gray;"><?php _e('(By default, categories do not hide)', 'wp-ds-blogmap'); ?></span></td>
                                    </tr>

                                </table>

                                <p class="submit">
                                    <input type="submit" name="wp_ds_blogmap_remove" class="button delete" value="<?php _e('Reset Settings', 'wp-ds-blogmap'); ?>" onclick="return confirm('<?php _e('Reset WP DS Blog Map settings to default value?', 'wp-ds-blogmap'); ?>')" />
                                    <input type="submit" name="submitted" value="<?php _e('Update Options', 'wp-ds-blogmap'); ?>" style="margin-left:1em;" />
                                </p>

                            </form>
                        	
                            </div>
    	                </div>
        	        </div>
            	</div>

	        </div>
        </div>

<?php 
    }
    # END wp_ds_blogmap_options ##################################------------------------------------------------------------#
    
    ##############################################################
    # wp_ds_blogmap_cat_checkbox()                               #
    #                                                            #
    # Внутренняя функция для вывода категорий в настройках       #
    #                                                            #
    # Входные параметры:                                         #
    #     $list - Список категорий на чекбоксах                  #
    #             которых надо взвести флажок                    #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_cat_checkbox($list = false){
        $result = '';
        $cats = get_categories();
        if($list) {
            foreach($cats as $cat) {
            $key = array_search($cat->cat_ID, $list);
                if($key === false){ $checked = ''; }else{ $checked = ' checked'; }
                $result .= '<input name="hidden_categories[]" type="checkbox" value="'.$cat->cat_ID.'" '.$checked.'/> '.$cat->name;
                $result .= '<BR />';
            }
        } else {
            foreach($cats as $cat) {
                $result .= '<input name="hidden_categories[]" type="checkbox" value="'.$cat->cat_ID.'" /> '.$cat->name;
                $result .= '<BR />';
            }
        }
        return $result;
    }
    # END wp_ds_blogmap_cat_checkbox #############################------------------------------------------------------------#
    
    ##############################################################
    # wp_ds_blogmap_cloud()                                      #
    #                                                            #
    # Предназначена для вывода облака тэгов                      #
    #                                                            #
    # Входные параметры:                                         #
    #     $limit - количество тэгов в облаке                     #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_cloud($limit = false){
        if($limit) {
            $settings = get_option('wp_ds_blogmap_array');

            $tags_limit = $settings['tags_limit'];
            $expand_text = $settings['expand_text'];
            $tags_count = count(wp_tag_cloud('number=0&format=array'));

            $moreLink = ($tags_limit && $tags_count > $tags_limit)?('<div class="pl_expand"><a href="#_" onclick="this.innerHTML=\''.__('Loading...', 'wp-ds-blogmap').'\'; pl_showcloud();">'.$expand_text.'</a></div>'):'';
        } else {
            $tags_limit = 0;
            $moreLink = '';
        }
        $tags = wp_tag_cloud('number='.$tags_limit.'&format=array');
        if($tags == ""){ return; }
        return implode(' ', $tags).$moreLink;
    }
    # END wp_ds_blogmap_cloud ####################################------------------------------------------------------------#

    ##############################################################
    # wp_ds_blogmap_getposts()                                   #
    #                                                            #
    # Предназначена для получения всех или части                 #
    #     постов у конкретной категории                          #
    #                                                            #
    # Входные параметры:                                         #
    #     $cat_id    - id категории                              #
    #     $limit     - Лимит который не должен превышаться       #
    #                   количеством записей в категории          #
    #     $cat_count - Количество записей в категории            #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_getposts($cat_id, $limit = false, $cat_count = 0) {
        if($limit) {
            $settings = get_option('wp_ds_blogmap_array');

            $posts_limit = $settings['posts_limit'];
            $expand_text = $settings['expand_text'];

            $moreLink = ($cat_count > $posts_limit)?("\n\n".'<div class="pl_expand"><a href="#_" onclick="this.innerHTML=\''.__('Loading...', 'wp-ds-blogmap').'\'; pl_showcat('.$cat_id.');">'.$expand_text.'</a></div>'):'';
        } else {
            $posts_limit = -1; // (http://codex.wordpress.org/Template_Tags/get_posts)
            $moreLink = '';
        }
        $posts = get_posts('numberposts='.$posts_limit.'&orderby=post_date&order=DESC&category='.$cat_id);
	
        $postList = array();
	
        foreach($posts as $post) {
            $postList[] = '<span class="pl_date">'.date('Y/m/d', strtotime($post->post_date)).'</span> <a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>';
        }
        return '<ul><li>'.implode('</li><li>', $postList).'</li></ul>'.$moreLink;
    }
    # END wp_ds_blogmap_getposts #################################------------------------------------------------------------#

    ##############################################################
    # wp_ds_blogmap_posts()                                      #
    #                                                            #
    # Предназначена для получения и вывода "облака" категорий и  #
    #     лимитированного списка постов к каждой их категорий    #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_posts(){
        $settings = get_option('wp_ds_blogmap_array');
    
        $prefix = $settings['prefix'];
        $posts_limit = $settings['posts_limit'];
        $posts_description = $settings['posts_description'];
        $hidden_categories = $settings['hidden_categories'];
        
        $cats = get_categories();
        
        $contents = array();
        $catList  = array();
	
        foreach($cats as $cat) {
            if ($hidden_categories != ''){
                $key = array_search($cat->cat_ID, $hidden_categories);
            }else{
                $key = false;
            }

            if($key === false){
                $postList = $this->wp_ds_blogmap_getposts($cat->cat_ID, ($posts_limit == 0)?false:true, $cat->count);
                $catId = 'cat_'.($cat->cat_ID);
                $cnt = '&nbsp;<span class="pl_cnt">('.$cat->count.')</span>';
                $contents[] = '<a href="#'.$catId.'">'.$cat->name.'</a>'.$cnt;
                  $catList[] = '<h3 class="pl_cat_title"><a href="" id="'.$catId.'"></a>'.(strlen($prefix)?('<span class="pl_prefix">'.$prefix.'</span> '):'').
                  $cat->name.$cnt.'&nbsp;<a href="#postlist_top" style="text-decoration:none;">&uarr;</a></h3>'.
                  ($posts_description?'<div id="pl_category_description">'.$cat->category_description.'</div>':'').
                  '<div id="pl_'.$catId.'" class="pl_postlist">'.$postList.'</div>'."\n\n";
            }
        }
        return '<div class="pl_contents"><a href="" id="postlist_top"></a>'.implode(', ', $contents).'</div><div id="result"></div>'."\n\n".implode("\n", $catList);
    }
    # END wp_ds_blogmap_posts ####################################------------------------------------------------------------#

    ##############################################################
    # wp_ds_blogmap_pages()                                      #
    #                                                            #
    # Предназначена для получения и вывода древовидного списка   #
    #     страниц                                                #
    #                                                            #
    ##############################################################------------------------------------------------------------#
    function wp_ds_blogmap_pages() {
        $settings = get_option('wp_ds_blogmap_array');
    
        $page_dept = $settings['page_dept'];
        $pages = wp_list_pages('depth='.$page_dept.'&title_li=0&sort_column=menu_order&echo=0');
    
        return '<div class="pl_pages"><ul>'.$pages.'</ul></div>';
    }
    # END wp_ds_blogmap_pages ####################################------------------------------------------------------------#    
}

$dsblogmap = new dsblogmap();



?>