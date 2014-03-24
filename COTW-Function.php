<?php
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

    // Initialize Champion List
    function COTW_Init()
    {
        global $COTW_CHAMPLIST;
        global $COTW_CHAMPWEEK;

        $COTW_CHAMPLIST = COTW_GetChampionsList();
        $COTW_CHAMPWEEK = COTW_GetChampionsOfTheWeek();
    }
    
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
?>