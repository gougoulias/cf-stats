<?php

$group_count=0;
$max_final_questions=0;
foreach ($counted as $parrent_group => $grouped1) {
	//echo '<h3>'.$parrent_group.'</h3>';
	foreach ($grouped1 as $groupped_value => $grouped2 ){
		//echo '<p>' . $groupped_value. '</p>';
		$group_count=$group_count+1;
		$final_questions=0;
		foreach ($grouped2 as $onegroup => $onegroupvalue) {
			//echo '<small>'.$onegroup.'</small> | ';
			$final_questions=$final_questions+1;
			$i=0;//counter for the answers
			(string)$group_name='groupname';
			(string)$group_name=(string)$group_name.(string)$group_count;
			// echo 'group name is : ' .$group_name;
			// echo ' | onegroup is : ' . $onegroup;
			foreach ($onegroupvalue as $label => $y) {
					$dataPoints[$group_name][$parrent_group][$groupped_value][$onegroup][$i]["label"]=$label;
					$dataPoints[$group_name][$parrent_group][$groupped_value][$onegroup][$i]["y"]=$y;
					$dataPointsdemo[$groupped_value][$onegroup][$i]["label"]=$label;
					$dataPointsdemo[$groupped_value][$onegroup][$i]["y"]=$y;
					$i=$i+1;
			}
		}
		if($max_final_questions<$final_questions){
		$max_final_questions=$final_questions;
		}
	}
}
echo 'DATAPOINTS<pre>';
//print_r($dataPoints);
echo '</pre>';
// echo "<br>";
// echo "<hr>";
// print_r($dataPointsGrouped);
echo "group count = " .$group_count ."<br>";
echo "max final questions = " . $max_final_questions; // autos einai o arithmos twn charts pou theleis oses diladi einai oi megistes erwtiseis

//try 1

// foreach ($dataPoints as $dPkey => $dPvalue) {
// 	foreach ($dPvalue as $groupnamekey => $groupnamevalue) {
// 		if($groupnamekey=='ungrouped'){
// 			foreach ($groupnamevalue as $gnvkey => $gnvvalue) {
// 				foreach ($gnvvalue as $lastkey => $lastvalue) {
// 					echo "<hr>";
// 					echo 'data ungrouped' ;
// 					print_r($lastvalue);
// 				}	
// 			}
// 		}else{
// 			foreach ($groupnamevalue as $gnvkey => $gnvvalue) {
// 				foreach ($gnvvalue as $lastkey => $lastvalue) {
// 					echo "<hr>";
// 					echo 'data ungrouped' ;
// 					print_r($lastvalue);
// 				}	
// 			}
// 		}
// 	}
// }

//try 2

foreach ($dataPointsdemo as $dPkey => $dPvalue) {
	//echo "- 1." . $dPkey . $dPvalue; 
	$all_groups[]=$dPkey;
	foreach ($dPvalue as $groupnamekey => $groupnamevalue) {
		//echo "- 2." .$groupnamekey;
		$allquestions[]=$groupnamekey;
		foreach ($groupnamevalue as $gnvkey => $gnvvalue) {
			//echo "- 3." .$gnvkey . $gnvvalue;
			foreach ($gnvvalue as $lastkey => $lastvalue) {
				//	echo "<hr>";
				//echo '-4 ' . $lastkey . $lastvalue;
				//echo "- 1." . $dPkey ." - 2." .$groupnamekey. " - 3." .$gnvkey." - 4." .$lastvalue;
				//echo "<br>";
			}	
		}

	}
}
echo "<br> all groups : ";
print_r($allgroups);
$all_groups_unique=array_unique($all_groups);
echo "<br> all groups unique: ";
print_r($all_groups_unique);
$all_questions_unique=array_unique($allquestions);
print_r($all_questions_unique);


// echo "<br>unique groups";
// print_r($all_groups_unique);
foreach ($all_questions_unique as $questionkey => $questionvalue) {
	//echo 'edw arxizei to chart' ;
	echo '<br>1. question : '. $questionvalue;
	foreach ($all_groups_unique as $groupkey => $groupvalue) {
		//echo "edw arxizei to kathe group loop  ";
		echo '<br>2. group : '. $groupvalue;
		echo '<br> this is the final: <br> ' ;
		print_r($dataPointsdemo[$groupvalue][$questionvalue]);
		//echo "edw teleiwnei to kathe group loop  ";

	}
	//echo 'edw teleiwnei  to chart' ;
}

//try 2 ends
?>



<!--starting the script-->
<script>
	window.onload = function () {
<?php 

foreach ($dataPoints as $dPkey => $dPvalue) {
	?>
	 
	var chart = new CanvasJS.Chart("chart-<?php echo $dPkey ;?>", {
		animationEnabled: true,
		theme: "light2",
		title:{
			text: "<?php echo $title; ?>"
		},
		axisY:{
			includeZero: true
		},
		legend:{
			cursor: "pointer",
			verticalAlign: "center",
			horizontalAlign: "right",
			itemclick: toggleDataSeries
		},
		data: [
			<?php
			foreach ($dPvalue as $groupnamekey => $groupnamevalue) {
			 	if($groupnamekey=='ungrouped'){ 
			 		foreach ($groupnamevalue as $gnvkey => $gnvvalue) { 
			 			foreach ($gnvvalue as $lastkey => $lastvalue) { ?>
							{
								type: "column",
								name: "<?php echo $gnvkey ; ?>",
								indexLabel: "{y}",
								yValueFormatString: "#0.##",
								showInLegend: true,
								dataPoints: <?php echo json_encode($lastvalue, JSON_NUMERIC_CHECK); ?>
							},
							<?php
						}
					} 
				}else{
					foreach ($groupnamevalue as $gnvkey => $gnvvalue) { 
						foreach ($gnvvalue as $lastkey => $lastvalue) { ?>
							{
								type: "column",
								name: "<?php echo $gnvkey ; ?>",
								indexLabel: "{y}",
								yValueFormatString: "#0.##",
								showInLegend: true,
								dataPoints: <?php echo json_encode($lastvalue, JSON_NUMERIC_CHECK); ?>
							},
							<?php 
						} 	
					} 
				} 
			} ?>
		]
	});
	chart.render();
	 
	function toggleDataSeries(e){
		if (typeof(e.dataSeries.visible) === "undefined" || e.dataSeries.visible) {
			e.dataSeries.visible = false;
		}
		else{
			e.dataSeries.visible = true;
		}
		chart.render();
	}
	 
	<?php	
}

?>
}
</script>
<!-- script end here -->
<?php
echo "<hr>";
echo "<br>=-=-=-=-=-=-=-=-=-=-=- EXAMPLE OF USE  =-=-==-=-==--==--=-==--=-==-</br>";
foreach ($dataPoints as $dPkey => $dPvalue) {
	?>
	<!-- create the div for each chart -->
	<div id="chart-<?php echo $dPkey ; ?>" style="height: 370px; width: 100%;"></div>
	<?php
}

?>

<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<?php




echo'<br>dataPoints<br><pre>';
//print_r($dataPoints1);
echo "</pre><br>";
//print_r(array_keys($count_ungrouped['ungrouped']['ungrouped']['age']));

// $dataPoints1 = array(
// 	array("label"=> "2010", "y"=> 36.12),
// 	array("label"=> "2011", "y"=> 34.87),
// 	array("label"=> "2012", "y"=> 40.30),
// 	array("label"=> "2013", "y"=> 35.30),
// 	array("label"=> "2014", "y"=> 39.50),
// 	array("label"=> "2015", "y"=> 50.82),
// 	array("label"=> "2016", "y"=> 74.70)
// );
// echo "<br>original datapoionts1<br>";
// print_r($dataPoints1);
// $dataPoints2 = array(
// 	array("label"=> "2010", "y"=> 64.61),
// 	array("label"=> "2011", "y"=> 70.55),
// 	array("label"=> "2012", "y"=> 72.50),
// 	array("label"=> "2013", "y"=> 81.30),
// 	array("label"=> "2014", "y"=> 63.60),
// 	array("label"=> "2015", "y"=> 69.38),
// 	array("label"=> "2016", "y"=> 98.70)
// );

?>

<?php ?>