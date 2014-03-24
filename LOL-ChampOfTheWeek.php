<?php
    /**
     * Plugin Name: League Of Legends - Champion Of The Week Widget
     * Plugin URI: https://github.com/azenko/LOL-ChampOfTheWeek/
     * Description: Sidebar Widget showing you the league of legends champions of the week.
     * Version: 0.1
     * Author: Valentin RESSEGUIER
     * Author URI: http://www.leguidedelinternaute.com
     * License: GPL2
     */

     /*  Copyright 2014  Valentin RESSEGUIER  (email : contact@leguidedelinternaute.com)

        This program is free software; you can redistribute it and/or modify
        it under the terms of the GNU General Public License, version 2, as 
        published by the Free Software Foundation.

        This program is distributed in the hope that it will be useful,
        but WITHOUT ANY WARRANTY; without even the implied warranty of
        MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        GNU General Public License for more details.

        You should have received a copy of the GNU General Public License
        along with this program; if not, write to the Free Software
        Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
    */

    /* Init Variable */
    $COTW_API_KEY = get_option('cotw_api_key');			// API KEY
    $COTW_API_REGION = get_option('cotw_srv_region');	// Server REGION
    $COTW_API_URL = get_option('cotw_api_url');			// API URI
    $COTW_SPRITEDIR_URI = "http://ddragon.leagueoflegends.com/cdn/" . get_option('cotw_cdn_version'); . "/img/sprite/";	// URI of the Sprite DIR of LoL Champ Icon
        
    $COTW_CHAMPLIST_URL = $COTW_API_URL."api/lol/static-data/".$COTW_API_REGION."/v1/champion?locale=en_EN&champData=image&api_key=".$COTW_API_KEY;   // API URI GET CHAMPIONS LIST
    $COTW_CHAMPWEEK_URL = $COTW_API_URL."api/lol/".$COTW_API_REGION."/v1.1/champion?freeToPlay=true&api_key=".$COTW_API_KEY;                          // API URI GET CHAMPIONS OF THE WEEK

    $COTW_CHAMPLIST = ""; 	// Future Array with the Champion List
    $COTW_CHAMPWEEK = "";	// Future Array with the Champion List Of The Week

    require plugin_dir_path( __FILE__ ).'COTW-Function.php';
    require plugin_dir_path( __FILE__ ).'COTW-Widget.php';
    require plugin_dir_path( __FILE__ ).'COTW-Admin.php';

    
?>

