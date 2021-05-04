<?php
/**
 * Plugin Name: CF stats
 * Plugin URI: https://github.com/gougoulias
 * Description: Contact form 7 submitions byy flamingo statistics view.
 * Version: 1.0
 * Author: Giannis Gougoulias
 * Author URI: https://github.com/gougoulias
 */

function cf_stats_plugin($atts){
 	// built the shorcode attributes
 		// name = the name of the contactform
		// group = the fileds that need to be grouped
	extract(shortcode_atts(array(
		'name'=> '',
		'group'=>'',	
	),$atts));

	$allgroups=explode(',', $group);
	//echo "all groups: ";
	//create empty array for each group that is reported in the shortcode parameter
	foreach ($allgroups as $sgroup) {
		//echo $sgroup . ', ';
		$groups_array=array();
		$groups_array[$sgroup]=array();
	}


	// check if the name parameter is given in the shortcode
	if ($name!=null){

		// set up the post arguments to get all the flamingo inbounds port types IDs and store them to flamingo_post variable
		$args =array(
			'post_type'=> 'flamingo_inbound',
			'fields'=> 'ids',
		);

		$flamingo_posts=get_posts($args);
		// echo "<br>flamingo posts :<br>";
		// print_r($flamingo_posts);
		// echo "<br>flamingo posts ends :<br>";

		//prepare regular expression
		$pattern = "/_field_/i";

		$temp_group= array();
		//get the fields values
		foreach ($flamingo_posts as $flp) {
			//echo "<br>flamingo post start<br>";
			// echo "<br>flamingo posts meta :<br>";
			// print_r(get_post_meta($flp));
			// echo "<br>flamingo posts meta ends :<br>";
			// echo '</br>-----<br>';

			//echo "<br>get taxonomies: <br> ";
			$flamingo_taxonomy=wp_get_object_terms($flp,'flamingo_inbound_channel');
			//print_r($flamingo_taxonomy);
			foreach ($flamingo_taxonomy as $fltax) {
			 	$flamingo_taxonomy_name=$fltax->name;
			 } 
			//echo "this post belongs to form with name : " . $flamingo_taxonomy_name ;
			//echo "<br>get taxonomies ends<br>";
			
			if ($flamingo_taxonomy_name==$name){
				echo "<br>Flamingo post start<br>";
				$get_the_keys=array_keys(get_post_meta($flp));
				//print_r($get_the_keys);
				$isgroup=false;
				foreach ($get_the_keys as $fkey) {
					if (preg_match($pattern, $fkey) ){
						echo 'to kleidi einai : ' .$fkey . '<br>';
						//$fkey=substr($fkey,7);
						
						//start grouping
						
						foreach ($allgroups as $sgroup) {
							$group_field='_field_';
							$group_field.=(string)$sgroup;
							//echo $group_field;
							if($fkey==$group_field){
								echo " einai group <br>";
								$isgroup=true;
								$temp_group[]=substr($fkey,7);
								$value=get_post_meta($flp)[$fkey][0];
								$regex_value='(.*:")';
								$replacement = '$1';
								$newvalue= preg_replace($regex_value, $replacement, $value);
								$regex_value='(".*)';
								$newvalue= preg_replace($regex_value, $replacement, $newvalue);
								echo 'kathari timi = ' . $newvalue .'<br>';
							// }else{
							// 	$isgroup=false;
								// edw prepei na epanalabw tin epanalipsi wste na kanw append sta arrays analoga me to group
								// edw einai i prwti allagi pou ginetai sto cloned apo ta ubuntu
							}
						}
						
						//auta edw prepe na fugoun kai na mpei i epanalispi ksana epanw
						//diladi tha exw mia epanalipsi gia na briskw to group
						// kai eswterika NEA epanalipsi gia  na apothikeyw tis times twn apotelesmatwn
						print_r(get_post_meta($flp)[$fkey]);
						if ($isgroup==true){
							foreach ($temp_group as $tg) {
								$groups_array[$tg][]=get_post_meta($flp)[$fkey];
								echo ' mpike sto array me to group ' . $tg .'';
							}
							
						}
						echo "<br>";
					}
				}
				echo "<br>Flamingo post Ends<br>";
			}
			//echo "<br>flamingo post Ends<br>--------------<br>";
		}
	}else{
		echo "<br>The shortcode parameter 'name=' is required <br>";
	}
	
	//print_r($groups_array);
	$json_records=json_encode($groups_array);
	echo 'this is the json :<br>' . $json_records .'<br>';
}

add_shortcode('cf-stats','cf_stats_plugin');