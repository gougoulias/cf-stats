<?php
//Save and retrieved cached data from/to database STARTS
if ($cached_setting=='on' && check_if_data_stored(get_the_post_id_that_used_the_shotcode()) ){
	//get the values from the database
	$dataPoints=json_decode(cf_stat_get_data(get_the_post_id_that_used_the_shotcode()),true);
	if (current_user_can('administrator')){
		if ($_POST['clear_cache']!='yes'){
		?>
		<form action="" method="post">
			<small><strong>Administrator note:</strong></small>
			<small>You see cached data stored in </small>
			<small>
				<?php
					echo cf_stat_get_last_update(get_the_post_id_that_used_the_shotcode());
				?>
			</small>
			<small>.<br> Please press </small>
			<input name="clear_cache" type="hidden" value="yes">
			<button class="button button-primary" type="submit">Clear Cache</button>
			<small>to update the data.</small>
		</form>
		<?php
		}
		if($_POST['clear_cache']=='yes'){
			cf_stat_clear_cached_data(get_the_post_id_that_used_the_shotcode());
			?>
			<form action="" method="post">
			<input name="clear_cache" type="hidden" value="no">
			<button class="button button-primary" type="submit">Refresh Page to update the data</button>
			</form>
		<?php

		}
	}
}else{
	// call the array counted for 1st time to find the total number of answers for each group STARTS
	foreach ($counted as $parrent_group => $grouped1) {
		//echo '<p>'.$parrent_group.'</p>';
		foreach ($grouped1 as $groupped_value => $grouped2 ){
			//echo '<p>' . $groupped_value. '</p>';
			$group_count=$group_count+1;
			foreach ($grouped2 as $onegroup => $onegroupvalue) {
				//try to find the total answers given for each group START
				foreach ($onegroupvalue as $label => $y) {
					if($parrent_group==$onegroup){ //check groups in order to get the number of answered products per group
						//echo $onegroup;
						$of_total[$groupped_value]=preg_replace('/[\'?\/\(\)\[\];]/', '', $y);
					}elseif ($parrent_group=='ungrouped') { // if not group then assignt to the total the total number of answers
						$of_total[$groupped_value]=$total_number_of_answers;
					}
				}
			}
		}
	}
	// call the array counted for 1st time to find the total number of answers for each group ENDS

	// call the array counted  for second time to assign the values so it can be viewable in the script adding label and y keys START
	foreach ($counted as $parrent_group => $grouped1) {
		//echo '<p>'.$parrent_group.'</p>';
		foreach ($grouped1 as $groupped_value => $grouped2 ){
			//echo '<p>' . $groupped_value. '</p>';
			$group_count=$group_count+1;
			foreach ($grouped2 as $onegroup => $onegroupvalue) {
				
				$i=0;//counter for the answers
				foreach ($onegroupvalue as $label => $y) {
					//preg replace special characters with space to prevent the brake of the script bellow
					$dataPoints[$groupped_value][$onegroup][$i]["label"]=preg_replace('/[\'?\/\(\)\[\];]/', '', $label);
					$dataPoints[$groupped_value][$onegroup][$i]["y"]=preg_replace('/[\'?\/\(\)\[\];]/', '', $y);
					$dataPoints[$groupped_value][$onegroup][$i]["of_total"]=$of_total[$groupped_value];//assign the of total groupped value from the previous loop that we have found the total number of answers for each group
					$i=$i+1;
				}
			}
		}
	}
	// call the array counted  for second time to assign the values so it can be viewable in the script adding label and y keys ENDS
	if ($cached_setting=='on'){
		$cached_dataPoints=json_encode($dataPoints);
		// check if there is any data stored in the db for the specific form
		if (check_if_data_stored(get_the_post_id_that_used_the_shotcode())){
			// do update with the latest values
			cf_stat_data_update($cached_dataPoints,get_the_post_id_that_used_the_shotcode());
		}else{
			// do insert the data for first time
			cf_stat_data_import($cached_dataPoints,get_the_post_id_that_used_the_shotcode());
		}
	}
}
//Save and retrieved cached data from/to database ENDS

//create arrays for groups START
foreach ($dataPoints as $dPkey => $dPvalue) {
	//echo "- 1." . $dPkey . $dPvalue; 
	$all_groups[]=$dPkey;
}
//create array for groups ENDS

//unique the groups
$all_groups_unique=array_unique($all_groups);

//create the arguments to get all forms ids STARTS
$args =array(
	'post_type'=> 'wpcf7_contact_form',
	'fields'=> 'ids',
	'nopaging'=> true,
);
$all_forms=get_posts($args);
//create the arguments to get all forms ids ENDS

//get all the formtags from the form that is called in shortcode name STARS
foreach ($all_forms as $cf7form) {
	//echo get_the_title($cf7form);
	if(get_the_title($cf7form)==$name){
		$ContactForm = WPCF7_ContactForm::get_instance( $cf7form );
		$form_fields = $ContactForm->scan_form_tags();
	}
}
//get all the formtags from the form that is called in shortcode name ENDS

//create store fields array and store inside all the names and the possible anwsers START
$store_fields= array();
foreach ($form_fields as $ffkey => $ffvalue) {
	//print_r($ffvalue->name);
	//print_r($ffvalue->labels);
	$store_fields[$ffvalue->name]=$ffvalue->labels;
}
//create store fields array and store inside all the names and the possible anwsers ENDS
?>

<!--starting the script-->
<script>
	window.onload = function () {
<?php 
foreach ($allstats as $questionkey => $questionvalue) {
	?>	 
	var chart<?php echo str_replace('-','',$questionvalue) ;?> = new CanvasJS.Chart("chart-<?php echo $questionvalue ;?>", {
		animationEnabled: true,
		theme: "light2",
		title:{
			text: "<?php echo $questionkey; ?>"
		},
		axisY:{
			includeZero: true
		},
		legend:{
			cursor: "pointer",
			verticalAlign: "bottom",
			horizontalAlign: "center",
			itemclick: toggleDataSeries<?php echo str_replace('-','',$questionvalue) ;?>
		},
		data: [
			<?php
			foreach ($all_groups_unique as $groupkey => $groupvalue) {
				if ($groupvalue=='ungrouped'){
					$groupvaluetitle='Συνολικές απαντήσεις';
				}else{
					$groupvaluetitle=$groupvalue;
				}
		 		 ?>
				{
					type: "column",
					name: "<?php echo $groupvaluetitle ; ?>",
					indexLabel: "{y}",
					<?php
					if ($percentage!='yes'){
						$valueformatsring="#0.##";
					}else{
						$valueformatsring="#0.##'%'";
					}
					?>
					yValueFormatString: "<?php echo $valueformatsring; ?>",
					showInLegend: true,
					//if the group value title is not the total make it non visible by default
					<?php if  ($groupvaluetitle!='Συνολικές απαντήσεις'){ ?>
						visible: false,
					<?php } ?>
					dataPoints: [<?php 
					//echo json_encode($dataPoints[$groupvalue][$questionvalue], JSON_NUMERIC_CHECK);
						foreach ($store_fields[$questionvalue] as $possibleanswerskey => $possibleanswersvalue) {
							(string)$possibleanswersvalue_fin='"';
							(string)$possibleanswersvalue_fin.=$possibleanswersvalue;
							(string)$possibleanswersvalue_fin.='"';
							$found=false;
							foreach ($dataPoints[$groupvalue][$questionvalue] as $real_answer_key => $real_answer_value) {
								if ($possibleanswersvalue_fin==$real_answer_value['label']){
									if ($percentage!='yes'){
										echo json_encode($real_answer_value, JSON_NUMERIC_CHECK);
									}else{
										$percentage_value=$real_answer_value['y']*100/$real_answer_value['of_total'];
										$printablearray=array("label"=> $real_answer_value['label'], "y"=> $percentage_value);
										echo json_encode($printablearray, JSON_NUMERIC_CHECK);
									}
									echo ",";
									$found=true;
								}
							}
							if ($found==false){
								if ($excludezero!='yes'){ //check shortcode value to include or not the zero values
									$temparray=array("label"=> $possibleanswersvalue_fin, "y"=> "0");
									echo json_encode($temparray, JSON_NUMERIC_CHECK);
									echo ",";
								}
							}
						}
					 ?>
				]},
				<?php		
			} ?>
		]
	});
	chart<?php echo str_replace('-','',$questionvalue) ;?>.render();
	 
	function toggleDataSeries<?php echo str_replace('-','',$questionvalue) ;?>(e){
		// create loop for each group so se visible only the selected one and hide others
		<?php foreach ($all_groups_unique as $groupnumber => $groupname) { ?>
			//console.log("epanalipsi" + <?php echo $groupnumber ?> );
			if (e.dataSeries.name=== e.chart.options.data[<?php echo $groupnumber; ?>].name){
				e.chart.options.data[<?php echo $groupnumber; ?>].visible = true;
			}else{
				e.chart.options.data[<?php echo $groupnumber ; ?>].visible = false;
			}
		<?php } ?>
		chart<?php echo str_replace('-','',$questionvalue) ;?>.render();
	}	 
	<?php		
}

?>
}
</script>
<!-- script end here -->
<?php

//create the loop for all divs the charts will be shown STARTS
//foreach ($all_questions_unique as $questionkey => $questionvalue) {
foreach ($allstats as $questionkey => $questionvalue) {
		?>
		<!-- create the div for each chart -->
		<div id="chart-<?php echo $questionvalue ; ?>" class="cf-stat-chart" style="height: 420px; width: 100%;"></div>
		<?php
}
//create the loop for all divs the charts will be shown ENDS
?>

<!--call the canvas js script-->
<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<?php
