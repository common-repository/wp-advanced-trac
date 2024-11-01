<?php
/*
Plugin Name: WP-Advanced-Trac
Plugin URI: http://eeexception.org/my-projects/wp-advanced-trac/
Description: Advanced version of the WP-Trac plugin. Helps to manage your projects and tasks. More usable for IT projects.
Version: 0.1
Author: Valery Konchin
Author URI: http://eeexception.org/my-projects/wp-advanced-trac/

Copyright 2010  Valery Konchin  (email : eeexception [at] gmail [dot] com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/
//error_reporting(E_ALL); 
//ini_set("display_errors", 1); 
//define('WP_DEBUG', true);

require_once ("AdvTracController.php");
require_once ("AdvTracModel.php");
$wptrac = new WP_ADV_Trac();

/**
 * @see WP_Trac::admin_menu()
 */
add_action('admin_head', array($wptrac , 'admin_head'));
/**
 * @see WP_Trac::admin_head()
 */
add_action('admin_menu', array($wptrac , 'admin_menu'));
/**
 * @see WP_Trac::short_code()
 */
add_shortcode('project', array($wptrac , 'short_code'));
/**
 * @see WP_Trac::install()
 */
register_activation_hook(__FILE__, array($wptrac , 'install'));

define("WPTRAC_REGEXP", "/\[wptrac ([[:print:]]+)\]/");
add_filter('the_content', array($wptrac , 'show_project_info'));
add_filter('the_content_rss', array($wptrac , 'show_project_info'));
add_filter('comment_text', array($wptrac , 'show_project_info'));
add_action( 'wp_print_scripts', array($wptrac , 'enqueue_plugin_scripts'));
add_action( 'wp_print_styles', array($wptrac , 'enqueue_plugin_styles'));

/**
 * @see http://codex.wordpress.org/Writing_a_Plugin
 */
class WP_ADV_Trac
{
	public function plugin_callback($match) {
		$wptrac = new AdvTracController();
		
		if(strlen($match[1]) > 0) {
			$output .= $wptrac->overview_project($match[1]);
		} else {
			$output .= $wptrac->overview_project();
		}
		return ($output);
	}

	/**
     * @see http://codex.wordpress.org/Function_Reference/add_action
     */
    public function show_project_info ($content)
    {
		$out = $content;
		$out = preg_replace_callback(WPTRAC_REGEXP, array(get_class($this), 'plugin_callback'), $out);
		return $out;
    }
    
    public function enqueue_plugin_scripts () {
		echo "<link type=\"text/css\" href=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/css/custom-theme/jquery-ui-1.7.2.custom.css\" rel=\"stylesheet\" />\n";
		echo "<script type=\"text/javascript\" src=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/js/jquery-1.3.2.min.js\"></script>\n";
		echo "<script type=\"text/javascript\" src=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/js/jquery-ui-1.7.2.custom.min.js\"></script>\n";
	}
	
	public function enqueue_plugin_styles () {
		echo "<link rel=\"stylesheet\" href=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/style.css\" type=\"text/css\" />\n";
        echo "<link rel=\"stylesheet\" href=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/global.css\" type=\"text/css\" />\n";    
	}

    /**
     * @see http://codex.wordpress.org/Function_Reference/add_action
     */
    public function admin_menu ()
    {
        $wptrac = new AdvTracController();
        add_menu_page('WP-Trac', 'WP-Advanced-Trac', 8, __FILE__, array(&$wptrac , 'overview'));
        add_submenu_page(__FILE__, 'Projects', 'Projects', 8, 'wp-advanced-trac/projects', array(&$wptrac , 'projects'));
        add_submenu_page(__FILE__, 'Tasks', 'Tasks', 8, 'wp-advanced-trac/tasks', array(&$wptrac , 'tasks'));
    }

    /**
     * @see http://codex.wordpress.org/Function_Reference/add_action
     */
    public function admin_head ()
    {
        if (strpos($_SERVER['REQUEST_URI'], 'wp-advanced-trac')) {
            echo "<link type=\"text/css\" href=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/css/custom-theme/jquery-ui-1.7.2.custom.css\" rel=\"stylesheet\" />\n";
            echo "<link rel=\"stylesheet\" href=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/style.css\" type=\"text/css\" />\n";
            echo "<script type=\"text/javascript\" src=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/js/jquery-1.3.2.min.js\"></script>\n";
            echo "<script type=\"text/javascript\" src=\"" . get_bloginfo('siteurl') . "/wp-content/plugins/wp-advanced-trac/js/jquery-ui-1.7.2.custom.min.js\"></script>\n";
        }
    }
    
    /**
     * @see http://codex.wordpress.org/Shortcode_API
     * @return
     */
    public function short_code ($atts)
    {
        $wptrac = new AdvTracController();
        return $wptrac->short_code($atts);
    }

    /**
     * @see http://codex.wordpress.org/Creating_Tables_with_Plugins
     */
    public function install ()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . "wp-trac-projects";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE `" . $table_name . "` (
                      `id` int(11) NOT NULL auto_increment,
                      `title` varchar(255) NOT NULL,
                      `project_home_url` varchar(255) NULL,
                      `project_issues_url` varchar(255) NULL,
                      `description` text NOT NULL,
                      `start` date NOT NULL,
                      `end` date NULL,
                      PRIMARY KEY  (`id`)
                    );";
            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
        }
        $table_name = $wpdb->prefix . "wp-trac-tasks";
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $sql = "CREATE TABLE `" . $table_name . "` (
                  `id` int(11) NOT NULL auto_increment,
                  `pid` int(11) NOT NULL,
                  `uid` int(11) NOT NULL default '0',
                  `title` varchar(255) NOT NULL,
                  `description` text NOT NULL,
                  `start` date NOT NULL,
                  `end` date NOT NULL,
                  `priority` int(11) NOT NULL,
                  `complete` int(11) NOT NULL default '0',
                  PRIMARY KEY  (`id`)
                );";
            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
            add_option("ion_db_version", 1);
        }
    }
}

?>
