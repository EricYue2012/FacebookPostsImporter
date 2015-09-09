<?php
/*
Plugin Name: News Challenge
Plugin URI: 
Description: This plugin pull out posts from Facebook and import them into WordPress site. 
Version: 0.1
Author: Eric Yue
Author URI: 

Copyright 2015  Eric Yue  (email : ericyue2012@gmail.com)

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
add_action( 'admin_menu', 'nc_admin_menu' );

function nc_admin_menu() {
	add_menu_page( 'Coding Challenge Plugin', 'News Challenge', 'manage_options', 'plugin/newschallenge.php', 'nc_admin_page', 'dashicons-tickets', 6  );
}

include 'admin/form.php';



