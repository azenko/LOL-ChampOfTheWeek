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
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA */
    
	/* Loading Mustache Engine */
	require plugin_dir_path( __FILE__ ).'/Mustache/Autoloader.php'; // Mustache Template Engine Loader
	Mustache_Autoloader::register();

	$COTW_MUSTACHEENGINE = new Mustache_Engine;   

	/* Mustache Templace Of The Widget */
	$COTW_TEMPLATE = "<div style='float:left;width:100;height:70;'>{{name}}<div style=\"display:block;height:48;width:48;background-image:url('{{url}}');background-position:left {{x}}px top {{y}}px;\"></div></div>";

	/* File of the Cache when Wordpress Cache Disabled */
	$COTW_CACHE = plugin_dir_path( __FILE__ ) . "./cache/COTW.tmp";

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