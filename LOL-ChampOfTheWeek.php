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

    $COTW_API_KEY = "0b90ba9e-4838-43b6-9e7a-435c5bc333ef";
    $COTW_API_REGION = "euw";
    $COTW_API_URL = "https://prod.api.pvp.net/";
    $COTW_SPRITEDIR_URI = "http://ddragon.leagueoflegends.com/cdn/4.4.3/img/sprite/"; // URI of the Sprite DIR of LoL Champ Icon
    
    $COTW_CHAMPLIST_URL = "http://prod.api.pvp.net/api/lol/static-data/".$COTW_API_REGION."/v1/champion?locale=fr_FR&champData=image&api_key=".$COTW_API_KEY;
    $COTW_CHAMPWEEK_URL = "http://prod.api.pvp.net/api/lol/".$COTW_API_REGION."/v1.1/champion?freeToPlay=true&api_key=".$COTW_API_KEY;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$COTW_CHAMPLIST_URL);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $returned = curl_exec($ch);
     curl_close ($ch);

    $chlist = json_decode($returned);
    $chlist = $chlist->data;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$COTW_CHAMPWEEK_URL);
    curl_setopt($ch, CURLOPT_VERBOSE, 1);
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $returned = curl_exec($ch);
    curl_close ($ch);

    $chweek = json_decode($returned);
    $chweek = $chweek->champions;

    foreach ($chweek as $COTW_CHAMPIONS) {
        foreach ($chlist as $COTW_IMAGES) {
            if($COTW_CHAMPIONS->name == $COTW_IMAGES->name)
            {
                $x = $COTW_IMAGES->image->x*-1;
                $y = $COTW_IMAGES->image->y*-1;
                echo "<div style='float:left;width:100;height:70;'>" . $COTW_IMAGES->name." : <div style=\"display:block;height:48;width:48;background-image:url('". $COTW_SPRITEDIR_URI . $COTW_IMAGES->image->sprite ."');background-position:left " . $x . " top " . $y ." ;\"></div></div>";
            }
        }  
    }
?>