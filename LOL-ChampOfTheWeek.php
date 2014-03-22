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
    require dirname(__FILE__).'/Mustache/Autoloader.php'; // Mustache Template Engine Loader

    $COTW_API_KEY = "0b90ba9e-4838-43b6-9e7a-435c5bc333ef";                                                                                                     // API KEY
    $COTW_API_REGION = "euw";                                                                                                                                   // Server REGION
    $COTW_API_URL = "https://prod.api.pvp.net/";                                                                                                                // API URI
    $COTW_SPRITEDIR_URI = "http://ddragon.leagueoflegends.com/cdn/4.4.3/img/sprite/";                                                                           // URI of the Sprite DIR of LoL Champ Icon
        
    $COTW_CHAMPLIST_URL = "http://prod.api.pvp.net/api/lol/static-data/".$COTW_API_REGION."/v1/champion?locale=fr_FR&champData=image&api_key=".$COTW_API_KEY;   // API URI GET CHAMPIONS LIST
    $COTW_CHAMPWEEK_URL = "http://prod.api.pvp.net/api/lol/".$COTW_API_REGION."/v1.1/champion?freeToPlay=true&api_key=".$COTW_API_KEY;                          // API URI GET CHAMPIONS OF THE WEEK

    $COTW_CHAMPLIST = "";
    $COTW_CHAMPWEEK = "";

    $COTW_CACHE = plugin_dir_path( __FILE__ ) . "cache/COTW.tmp";

    function COTW_GetChampionsList()
    {
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
    
    function COTW_CacheEnabled()
    {
        wp_cache_set( 'COTW_CACHEORNOTCACHE', "CacheWork?", "COTW", 60);
        $COTW_CACHETEST = wp_cache_get('COTW_CACHEORNOTCACHE');
        if ( false === $COTW_CACHETEST ) {
            return false;
        }
        return true; 
    }

    function COTW_GetChampionsOfTheWeek()
    {
        global $COTW_CHAMPWEEK_URL;
        if(COTW_CacheEnabled())
        {
            $result = wp_cache_get('COWT_CHAMPOFWEEK');
            if ( false === $result ) {
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
                wp_cache_set( 'COWT_CHAMPOFWEEK', $chweek, "COTW", 3600);
            } 
        } else {
            global $COTW_CACHE;
            // Get Champions Of The Week
            // Verify if data are cached
            if(time() - fileatime($COTW_CACHE) > 3600)
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

                file_put_contents($COTW_CACHE, serialize($chweek));
            } else {
                $chweek = unserialize(file_get_contents($COTW_CACHE));
            }
        }
        // Return Champions Array
        return $chweek;
    }

    function COTW_Init()
    {
        global $COTW_CHAMPLIST;
        global $COTW_CHAMPWEEK;

        $COTW_CHAMPLIST = COTW_GetChampionsList();
        $COTW_CHAMPWEEK = COTW_GetChampionsOfTheWeek();
    }
    
    function COTW_Show()
    {
        global $COTW_CHAMPLIST;
        global $COTW_CHAMPWEEK;
        global $COTW_SPRITEDIR_URI;

        foreach ($COTW_CHAMPWEEK as $COTW_CHAMPIONS) {
            foreach ($COTW_CHAMPLIST as $COTW_IMAGES) {
                if($COTW_CHAMPIONS->name == $COTW_IMAGES->name)
                {
                    $x = $COTW_IMAGES->image->x*-1;
                    $y = $COTW_IMAGES->image->y*-1;
                    echo "<div style='float:left;width:100;height:70;'>" . $COTW_IMAGES->name." : <div style=\"display:block;height:48;width:48;background-image:url('". $COTW_SPRITEDIR_URI . $COTW_IMAGES->image->sprite ."');background-position:left " . $x . "px top " . $y ."px;\"></div></div>";
                }
            }  
        }
    }
?>