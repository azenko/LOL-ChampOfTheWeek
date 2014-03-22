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

    /* Loading Mustache Engine */
    require plugin_dir_path( __FILE__ ).'/Mustache/Autoloader.php'; // Mustache Template Engine Loader
    Mustache_Autoloader::register();

    /* Init Variable */
    $COTW_API_KEY = "0b90ba9e-4838-43b6-9e7a-435c5bc333ef";                                                                                                     // API KEY
    $COTW_API_REGION = "euw";                                                                                                                                   // Server REGION
    $COTW_API_URL = "https://prod.api.pvp.net/";                                                                                                                // API URI
    $COTW_SPRITEDIR_URI = "http://ddragon.leagueoflegends.com/cdn/4.4.3/img/sprite/";                                                                           // URI of the Sprite DIR of LoL Champ Icon
        
    $COTW_CHAMPLIST_URL = "http://prod.api.pvp.net/api/lol/static-data/".$COTW_API_REGION."/v1/champion?locale=fr_FR&champData=image&api_key=".$COTW_API_KEY;   // API URI GET CHAMPIONS LIST
    $COTW_CHAMPWEEK_URL = "http://prod.api.pvp.net/api/lol/".$COTW_API_REGION."/v1.1/champion?freeToPlay=true&api_key=".$COTW_API_KEY;                          // API URI GET CHAMPIONS OF THE WEEK

    $COTW_CHAMPLIST = "";                                                                                                                                       // Future Array with the Champion List
    $COTW_CHAMPWEEK = "";                                                                                                                                       // Future Array with the Champion List Of The Week
    $COTW_MUSTACHEENGINE = new Mustache_Engine;                                                                                                                 // Mustache Engine Init

    /* Mustache Templace Of The Widget */
    $COTW_TEMPLATE = "<div style='float:left;width:100;height:70;'>{{name}}<div style=\"display:block;height:48;width:48;background-image:url('{{url}}');background-position:left {{x}}px top {{y}}px;\"></div></div>";

    /* File of the Cache when Wordpress Cache Disabled */
    $COTW_CACHE = plugin_dir_path( __FILE__ ) . "./cache/COTW.tmp";

    /* Function to get the champion list */
    function COTW_GetChampionsList()
    {
        /* Loading Variable for a function Use */
        global $COTW_CHAMPLIST_URL;

        // Get Champions List
        // API REQUEST
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$COTW_CHAMPLIST_URL);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        $returned = curl_exec($ch);
         curl_close ($ch);
        // API JSON DECODE
        $chlist = json_decode($returned);
        $chlist = $chlist->data;

        // Return Champions Array
        return $chlist;
    }
    
    /* Test if the wordpress cache is enabled */
    function COTW_CacheEnabled()
    {
        wp_cache_set( 'COTW_CACHEORNOTCACHE', "CacheWork?", "COTW", 60); // Try to add a fake cache value for 60s
        $COTW_CACHETEST = wp_cache_get('COTW_CACHEORNOTCACHE'); // Fake Value exist ?
        if ( false === $COTW_CACHETEST ) {
            return false; // Cache Disabled
        }
        return true;  // Cache Enabled
    }

    /* Function to get the list of champion of the week */
    function COTW_GetChampionsOfTheWeek()
    {
        /* Globalize Variable */
        global $COTW_CHAMPWEEK_URL;

        if(COTW_CacheEnabled()) // Cache Enabled ? If True use WP Cache
        {
            $chweek = wp_cache_get('COWT_CHAMPOFWEEK'); // Try to get the list in the cache
            if ( false === $chweek ) {  // Data not retrived ? Yes get it
                // API REQUEST
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$COTW_CHAMPWEEK_URL);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                $returned = curl_exec($ch);
                curl_close ($ch);

                // API JSON DECODE
                $chweek = json_decode($returned);
                $chweek = $chweek->champions;

                // Add data in the cache for a hour
                wp_cache_set( 'COWT_CHAMPOFWEEK', $chweek, "COTW", 3600);
            } 
        } else { // Else use Plugin Cache
            /* Globalize Where is the Cache File */
            global $COTW_CACHE;

            // Get Champions Of The Week
            // Verify if data are cached in the last hour
            if(time() - fileatime($COTW_CACHE) > 3600) /* if no update cache */
            {
                // API REQUEST
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL,$COTW_CHAMPWEEK_URL);
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                $returned = curl_exec($ch);
                curl_close ($ch);

                // API JSON DECODE
                $chweek = json_decode($returned);
                $chweek = $chweek->champions;

                // Add data in the cache
                file_put_contents($COTW_CACHE, serialize($chweek));
            } else { /* if yes */
                // Get data from the cache file
                $chweek = unserialize(file_get_contents($COTW_CACHE));
            }
        }
        // Return Champions Array
        return $chweek;
    }

    // Initialize Champion List
    function COTW_Init()
    {
        global $COTW_CHAMPLIST;
        global $COTW_CHAMPWEEK;

        $COTW_CHAMPLIST = COTW_GetChampionsList();
        $COTW_CHAMPWEEK = COTW_GetChampionsOfTheWeek();
    }

    // Show the widget
    function COTW_Show()
    {
        global $COTW_CHAMPLIST, $COTW_CHAMPWEEK, $COTW_SPRITEDIR_URI, $COTW_TEMPLATE, $COTW_MUSTACHEENGINE;
        foreach ($COTW_CHAMPWEEK as $COTW_CHAMPIONS) {
            foreach ($COTW_CHAMPLIST as $COTW_IMAGES) {
                if($COTW_CHAMPIONS->name == $COTW_IMAGES->name)
                {
                    $name = $COTW_IMAGES->name;
                    $url = $COTW_SPRITEDIR_URI.$COTW_IMAGES->image->sprite;
                    $x = $COTW_IMAGES->image->x*-1;
                    $y = $COTW_IMAGES->image->y*-1;
                    $data = array('name' => $name, 
                                    'url' => $url,
                                    'x' => $x,
                                    'y' => $y);
                    echo $COTW_MUSTACHEENGINE->render($COTW_TEMPLATE, $data);
                }
            }  
        }
    }
?>

