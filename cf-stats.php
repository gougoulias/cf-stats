<?php
/**
 * Plugin Name: CF stats
 * Plugin URI: https://github.com/gougoulias/cf-stats
 * Description: Contact form 7 submitions by flamingo statistics view.
 * Version: 1.0
 * Author: Giannis Gougoulias
 * Author URI: https://github.com/gougoulias
 */

function cf_stats_plugin($atts){
 	// built the shorcode attributes
 		// name = the name of the contactform
		// stats = the elemennts that will be countable
		// group = the fields that need to be grouped
	extract(shortcode_atts(array(
		'name'=> '',
		'stats'=>'',
		'group'=>'',	
	),$atts));

	if ($stats!=null){
		// make an array with all the stats reported in the shortcode parameters
		$allstats=explode(',',$stats);
		//make second array for the stats to add later the grouped ones
		$allstats_plus_groupped=explode(',',$stats);
	}
	
	if ($group!=null){
		//make an array with all the groups reported in the shortcode parameters
		$allgroups=explode(',', $group);
	}

	//adding the grouped one stats to the allstatsplus grouped to be counted too regardind if they will be shown or not STARTS
	foreach ($allgroups as $groupkey => $groupvalue) {
		$allstats_plus_groupped[]=$groupvalue;
	}
	$allstats_plus_groupped=array_unique($allstats_plus_groupped);
	//print_r($allstats_plus_groupped);
	//adding the grouped one stats to the allstatsplus grouped to be counted too regardind if they will be shown or not ENDS

	// check if the name parameter and the stats parametere is given in the shortcode
	if ($name!=null && $stats!=null){

		// set up the post arguments to get all the flamingo inbounds port types IDs and store them to flamingo_post variable
		$args =array(
			'post_type'=> 'flamingo_inbound',
			'fields'=> 'ids',
			'nopaging'=> true,
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
			
			//check the form name to start collecting values
			if ($flamingo_taxonomy_name==$name){
				//Flamingo post start
				// get all the keys of the flamingo post 
				$get_the_keys=array_keys(get_post_meta($flp));
				//print_r($get_the_keys);
				//$isgroup=false;
				foreach ($get_the_keys as $fkey) {
					//check if the key is form input field or other type of form information
					if (preg_match('/_field_/i', $fkey) ){

						foreach ($allstats_plus_groupped as $statvalue) {
							$stat_field='_field_';
							$stat_field.=(string)$statvalue;
							if($fkey==$stat_field){ // check if the field is countable or not
								if ($allgroups){
									//start grouping
									foreach ($allgroups as $sgroup) {
										$group_field='_field_';
										$group_field.=(string)$sgroup;
										//echo $group_field;
										if($fkey==$group_field){// check if the field is grouped or not
											//echo "<br>--------group  ". $group_field ." starts HERE--------------<br>";
											$group_name=substr($fkey,7);
											// get the value of the form field
											$value=get_post_meta($flp)[$fkey][0];
											//clean the value of not essential elements for the grouping face start
											$regex_value='(.*:")';
											$replacement = '$1';
											$newvalue= preg_replace($regex_value, $replacement, $value);
											$regex_value='(".*)';
											$newvalue= preg_replace($regex_value, $replacement, $newvalue);
											//clean the value of not essential elements for the grouping face ends
											//echo 'kathari timi = ' . $newvalue .'<br>';
											//second loop of the form fields starts here
											foreach ($get_the_keys as $fkeygrouped) {
												//check if the key is form input field or other type of form information
												if (preg_match('/_field_/i', $fkeygrouped) ){
													foreach ($allstats_plus_groupped as $statvalue_grouped) {
														$stat_field_grouped='_field_';
														$stat_field_grouped.=(string)$statvalue_grouped;
														if($fkeygrouped==$stat_field_grouped){ // check if the field is countable or not for the grouped value
															//echo 'to kleidi einai : ' .$fkeygrouped . '<br>';
															//print_r(get_post_meta($flp)[$fkeygrouped]);
															//echo '</br>';
															// get the value of the form field for the grouuped value
															$valuegrouped=get_post_meta($flp)[$fkeygrouped][0];
															$regex_value='/("[\w\d\sαβγδεζηθικλμνξοπρστυφχψωςΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫ&+?-?-]+")/i';
															//clean the value of not essential elements 
															preg_match_all($regex_value, $valuegrouped, $matches_grouped);
															//separate multiple values e.g. multiselect or checkbox
															$separate_matches_grouped= implode(", ",$matches_grouped[1]);
															//create array for all grouped values
															$all_matches_grouped= explode(", ",$separate_matches_grouped);
															foreach ($all_matches_grouped as $fin_value_grouped) {
																// store the grouped values to the array
																$groups_array[$group_name][$newvalue][$statvalue_grouped][]=$fin_value_grouped;
																//create array and store the count of each values per group
																$counted[$group_name][$newvalue][$statvalue_grouped]=array_count_values($groups_array[$group_name][$newvalue][$statvalue_grouped]);
															}
														}
													}	
												}
											}//second loop of the form fiedls ends here
											//echo "<br>--------group ". $group_field ." ENDS HERE--------------<br>";
										}
									}//end grouping
								}//end ckeck for groups
								//print_r(get_post_meta($flp)[$fkey]);
								// get the value of the form field for the ungrouuped value
								$value_ungrouped=get_post_meta($flp)[$fkey][0];
								$regex_value='/("[\w\d\sαβγδεζηθικλμνξοπρστυφχψωςΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩάέήίόύώΆΈΉΊΌΎΏϊϋΪΫ&+?-?-]+")/i';
								//clean the value of not essential elements
								preg_match_all($regex_value, $value_ungrouped, $matches_ungrouped);
								//separate multiple values e.g. multiselect or checkbox
								$separate_matches_ungrouped=implode(", ", $matches_ungrouped[1]);
								//create array for all ungrouped values
								$all_matches_ungrouped=explode(", ",$separate_matches_ungrouped);
								foreach ($all_matches_ungrouped as $fin_value_ungrouped) {
									//store the ungrouped values to the array (ungrouped -> ungrouped)
									$groups_array['ungrouped']['ungrouped'][$statvalue][]=$fin_value_ungrouped;
									//create array and store the count of each values for ungrouped values
									$counted['ungrouped']['ungrouped'][$statvalue]=array_count_values($groups_array['ungrouped']['ungrouped'][$statvalue]);
								}
								//echo "<br>";
							}
						}	
					}
				}
				// Flamingo post Ends
			}// end if for check name
		}
	}else{
		//print message to user to use the shortcode parameters name and stats as they are required
		echo "<br>The shortcode parameter 'name=' and 'stats=' is required <br>";
	}
	// echo 'groups arrays is <br>';
	// echo'<pre>';
	// print_r($groups_array);
	// echo'</pre>';

	// //make the array json format
	// $json_records=json_encode($groups_array);
	// echo 'this is the json :<br>' . $json_records .'<br>';

	include('visual.php');
	
}

add_shortcode('cf-stats','cf_stats_plugin');