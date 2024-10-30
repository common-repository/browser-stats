<?php
/*
Plugin Name: Browser_Stats
Plugin URI: Your Plugin URI
Version: Version 1.0
Author: Jason T.  Dicks
Description: Displays the information about what browser your visitors are using and returns that as a percentage in a widget.

  Copyright YEAR  Jason Dicks  (email : jtd361@gmail.comL)

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

################################################################
##				         Set the cookie		                  ##
################################################################

//set the cookie
//setcookie(uniquehit, date("y-m-d"),time()+60*60*24*365);

/*//check for cookie
if(isset($_COOKIE['uniquehit']))
{ 
echo "cookie is set";
echo "<br />";
}else{
*/

################################################################
##				Create DB tables and activation 		      ##
################################################################
	add_option("browserstats_db_version", "1.0");
	
	function create_table() {
	//create browser table
	global $wpdb;
	$table_name = $wpdb->prefix . "browser";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		 $sql = "CREATE TABLE " . $table_name . " (
		 	id mediumint(10) NOT NULL AUTO_INCREMENT,
  			FireFox varchar(20) NOT NULL,
  			Safari varchar(20) NOT NULL,
  			ie7 varchar(20) NOT NULL,
  			netscape varchar(20) NOT NULL,
  			ie6 varchar(20) NOT NULL,
  			ie5 varchar(20) NOT NULL,
  			opera varchar(20) NOT NULL,
  			other varchar(20) NOT NULL,
  			UNIQUE KEY id  (id)
			)";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
				
				$insert = "INSERT INTO " . $table_name .
            " (FireFox, Safari, ie7, netscape, ie6, ie5, opera, other, id) " .
            "VALUES (0,0,0,0,0,0,0,0,0)";

      		$results = $wpdb->query( $insert );
				
		}
		
		//create counter table
	
	$table2_name = $wpdb->prefix . "counter";
		if($wpdb->get_var("SHOW TABLES LIKE '$table2_name'") != $table2_name) {
		 $sql = "CREATE TABLE " . $table2_name . " (
		 	id mediumint(10) NOT NULL AUTO_INCREMENT,
  			hits varchar(20) NOT NULL,
  			UNIQUE KEY id  (id)
			)";
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				dbDelta($sql);
				
				$insert = "INSERT INTO " . $table2_name .
            " (hits) " .
            "VALUES ('0')";

      		$results = $wpdb->query( $insert );
				
		}
		}
register_activation_hook(__FILE__,'create_table');

################################################################
##				function to determin browser type		      ##
################################################################
//create the function
function display_browser_stats() { 
$content = '';

//select/query data from table
$result = mysql_query("SELECT * FROM wp_browser");
$result2 = mysql_query("SELECT * FROM wp_counter");

//get row data and make array
$row = mysql_fetch_array($result);
$row2 = mysql_fetch_array($result2);


//gets the browser info
$browser = $_SERVER['HTTP_USER_AGENT'];

//if browser is firefox increment firefox data
if(stristr($browser,'Firefox') && (!stristr($browser,'Navigator'))) {
	mysql_query("UPDATE wp_browser SET FireFox = FireFox + 1");
	echo "|Current browser: ".stristr($browser,'Firefox')."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}

//if browser is safari increment safari data and add to count
else if(stristr($browser,'Safari')) {
	mysql_query("UPDATE wp_browser SET Safari = Safari + 1");
	echo "|Current browser: ".stristr($browser,'Safari')."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}

//if browser is ie7 increment ie7 data
else if(stristr($browser,'MSIE 7')) {
	mysql_query("UPDATE wp_browser SET ie7 = ie7 + 1");
	echo "|Current browser: ".stristr($browser,'MSIE 7')."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}
//if browser is ie6 increment ie6 data
else if(stristr($browser,'MSIE 6')) {
	mysql_query("UPDATE wp_browser SET ie6 = ie6 + 1");
	echo "|Current browser: ".stristr($browser,'MSIE 6')."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}
//if browser is netscape increment netscape data
else if(stristr($browser,'Navigator') && stristr($browser,'Firefox') || stristr($browser,'Netscape')) {
	mysql_query("UPDATE wp_browser SET netscape = netscape + 1");
	echo "|Current browser: ".stristr($browser,'Navigator')."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}
//if browser is opera increment opera data
else if(stristr($browser,'opera')) {
	mysql_query("UPDATE wp_browser SET opera = opera + 1");
	echo "|current browser: ".$browser."|";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}
else {
	mysql_query("UPDATE wp_browser SET other = other + 1");
	echo "Your browser: ".$browser." is not on the list!";
	mysql_query("UPDATE wp_counter SET hits = hits + 1");
}
################################################################
##				       Display Content        		          ##
################################################################

$ffpercent =  @round(($row['FireFox'] / $row2['hits']) * 100);
$sfpercent =  @round(($row['Safari'] / $row2['hits']) * 100);
$ie7percent =  @round(($row['ie7'] / $row2['hits']) * 100);
$ie6percent =  @round(($row['ie6'] / $row2['hits']) * 100);
$ie5percent =  @round(($row['ie5'] / $row2['hits']) * 100);
$nspercent =  @round(($row['netscape'] / $row2['hits']) * 100);
$oppercent =  @round(($row['opera'] / $row2['hits']) * 100);
$othpercent =  @round(($row['other'] / $row2['hits']) * 100);

echo'
<style>
    .graph { 
        position: relative;
        width: 80px; 
        border: 1px solid #B1D632; 
        padding: 2px; 
    }
    .graph .bar { 
        display: block;
        position: relative;
        background: #B1D632; 
        text-align: center; 
        color: #333; 
        height: 1em; 
        line-height: 1em;            
    }
    .graph .bar span { position: absolute; left: 1em; }
</style>
<table border="0">
	<tr>
		<td>
			Firefox
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $ffpercent;echo'%;">';echo $ffpercent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			Safari
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $sfpercent;echo'%;">';echo $sfpercent;echo'%</strong>
			</div>
		</td>
	</tr>	
	<tr>
		<td>
			IE7
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $ie7percent;echo'%;">';echo $ie7percent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			IE6
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $ie6percent;echo'%;">';echo $ie6percent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			IE5
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $ie5percent;echo'%;">';echo $ie5percent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			Netscape
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $nspercent;echo'%;">';echo $nspercent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			Opera
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $oppercent;echo'%;">';echo $oppercent;echo'%</strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			Other
		</td>
		<td>
			<div class="graph">
				<strong class="bar" style="width:';echo $othpercent;echo'%;">';echo $othpercent;echo'%</strong>
			</div>
		</td>
	</tr>
</table>
';


}
################################################################
## 			      Creates widgets Functionality 			  ##
################################################################
function widget_statistics() {
?>
  <h2 class="widgettitle">Browser Stats</h2>
  <?php display_browser_stats(); ?>
<?php
}

function statistics_init()
{
  register_sidebar_widget(__('Browser Stats'), 'widget_statistics');     
}
add_action("plugins_loaded", "statistics_init");

//}
?>
