<?php
/**
 * Plugin Name: CF stats
 * Plugin URI: https://github.com/gougoulias
 * Description: Contact form 7 submitions by flamingo statistics view.
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
		'stats'=>'',
		'group'=>'',	
	),$atts));

	if ($stats!=null){
	$allstats=explode(',',$stats);
	}
	
	$allgroups=explode(',', $group);
	//echo "all groups: ";
	//create empty array for each group that is reported in the shortcode parameter
	foreach ($allgroups as $sgroup) {
		//echo $sgroup . ', ';
		$groups_array=array();
		$groups_array[$sgroup]=array();
	}


	// check if the name parameter is given in the shortcode
	if ($name!=null && $stats!=null){

		// set up the post arguments to get all the flamingo inbounds port types IDs and store them to flamingo_post variable
		$args =array(
			'post_type'=> 'flamingo_inbound',
			'fields'=> 'ids',
		);

		$flamingo_posts=get_posts($args);
		
		//get the fields values
		foreach ($flamingo_posts as $flp) {

			//get taxonomies 
			$flamingo_taxonomy=wp_get_object_terms($flp,'flamingo_inbound_channel');
			//print_r($flamingo_taxonomy);
			foreach ($flamingo_taxonomy as $fltax) {
			 	$flamingo_taxonomy_name=$fltax->name;
			 } 
			//echo "this post belongs to form with name : " . $flamingo_taxonomy_name ;
			//get taxonomies ends
			
			if ($flamingo_taxonomy_name==$name){
				echo "<br>Flamingo post start<br>";
				$get_the_keys=array_keys(get_post_meta($flp));
				//print_r($get_the_keys);
				//$isgroup=false;
				foreach ($get_the_keys as $fkey) {
					if (preg_match('/_field_/i', $fkey) ){

						//print_r($allstats);
						foreach ($allstats as $statvalue) {
							$stat_field='_field_';
							$stat_field.=(string)$statvalue;
							if($fkey==$stat_field){ //elegxos an to field prepei na metrithei
								
								//start grouping
								foreach ($allgroups as $sgroup) {
									$group_field='_field_';
									$group_field.=(string)$sgroup;
									//echo $group_field;
									if($fkey==$group_field){//elegxos an anikei se group
										//echo " einai group <br>";
										echo "<br>--------group  ". $group_field ." starts HERE--------------<br>";
										$group_name=substr($fkey,7);
										//echo "GROUP NAME = ". $group_name . '<br>';
										$value=get_post_meta($flp)[$fkey][0];
										$regex_value='(.*:")';
										$replacement = '$1';
										$newvalue= preg_replace($regex_value, $replacement, $value);
										$regex_value='(".*)';
										$newvalue= preg_replace($regex_value, $replacement, $newvalue);
										//echo 'kathari timi = ' . $newvalue .'<br>';
										//second loop starts here
										foreach ($get_the_keys as $fkeygrouped) {
											if (preg_match('/_field_/i', $fkeygrouped) ){
												foreach ($allstats as $statvalue) {
													$stat_field_grouped='_field_';
													$stat_field_grouped.=(string)$statvalue;
													if($fkeygrouped==$stat_field_grouped){ //elegxos an to field prepei na metrithei gia ta grouped
														//echo 'to kleidi einai : ' .$fkeygrouped . '<br>';
														//print_r(get_post_meta($flp)[$fkeygrouped]);
														//echo '</br>';
														$valuegrouped=get_post_meta($flp)[$fkeygrouped][0];
														$regex_value='/("[\w\d\sαβγδεζηθικλμνξοπρστυφχψωςΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫ&?-?-]+")/i';
														preg_match_all($regex_value, $valuegrouped, $matches_grouped);
														$all_matches_grouped= implode(", ",$matches_grouped[1]);
														$groups_array[$group_name][$newvalue][$fkeygrouped][]=$all_matches_grouped;
													}
												}	
											}
										}
										echo "<br>--------group ". $group_field ." ENDS HERE--------------<br>";
									}
								}//end grouping

								//echo 'to kleidi einai : ' .$fkey . '<br>';
								//print_r(get_post_meta($flp)[$fkey]);
								//echo '</br>';
								$value_ungrouped=get_post_meta($flp)[$fkey][0];
								$regex_value='/("[\w\d\sαβγδεζηθικλμνξοπρστυφχψωςΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫ&?-?-]+")/i';
								preg_match_all($regex_value, $value_ungrouped, $matches_ungrouped);
								$all_matches_ungrouped=implode(", ", $matches_ungrouped[1]);
								$groups_array['ungrouped']['ungrouped'][$fkey][]=$all_matches_ungrouped;
								echo "<br>";
							}
						}	
					}
				}
				echo "<br>Flamingo post Ends<br>";
			}
		}
	}else{
		echo "<br>The shortcode parameter 'name=' and 'stats=' is required <br>";
	}
	echo 'groups arrays is <br>';
	//echo'<pre>';
	print_r($groups_array);
	//echo'</pre>';
	// how to count 1st try starts
	echo '<br>counting starts<br><pre>';
	foreach ($groups_array as $ga) {
		//print_r($ga)
		foreach ($ga as $cgroups){
			//print_r($cgroups);
			foreach ($cgroups as $cvalues) {
				//print_r($cvalues);
				print_r(array_count_values($cvalues));
				//  foreach ($cvalues as $cv) {
				// // 	//print_r($cv);
				// 	print_r(array_count_values($cv));
				//  }
			}	
		}	
	}

	//print_r(array_count_values($groups_array));
	echo '</pre><br>counting ends<br>';
	// how to count 1st try ends
	echo "<br>------------<br>";
	//print_r($groups_array);
	$json_records=json_encode($groups_array);
	echo 'this is the json :<br>' . $json_records .'<br>';
}

add_shortcode('cf-stats','cf_stats_plugin');