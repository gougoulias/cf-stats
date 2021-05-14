<?php 
//demo array
echo "<br>=-=-=-=-=-==-=-=-=-=-DEMO -=-==-=--=-==-=-=--=-=-=-=-=-=-<br>";
//print_r($count_ungrouped['ungrouped']['ungrouped']);
echo "<br>";

foreach ($counted as $parrent_group => $grouped1) {
	echo '<h2>'.$parrent_group.'</h2>';
	foreach ($grouped1 as $groupped_value => $grouped2 ){
		echo '<h3>' . $groupped_value. '</h3>';
		foreach ($grouped2 as $onegroup => $onegroupvalue) {
			echo '<h3>' . $onegroup. '</h3>';
			$i=0;
			foreach ($onegroupvalue as $label => $y) {
				$dataPoints1[$parrent_group][$groupped_value][$onegroup][$i]["label"]=$label;
				$dataPoints1[$parrent_group][$groupped_value][$onegroup][$i]["y"]=$y;
				$i=$i+1;
			}
			?>

			<script>
			window.onload = function () {
			 
			var chart = new CanvasJS.Chart("chart-<?php echo $parrent_group.'-'.$groupped_value.'-'.$onegroup ;?>", {
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
				data: [{
					type: "column",
					name: "<?php echo $groupped_value ; ?>",
					indexLabel: "{y}",
					yValueFormatString: "#0.##",
					showInLegend: true,
					dataPoints: <?php echo json_encode($dataPoints1[$parrent_group][$groupped_value][$onegroup], JSON_NUMERIC_CHECK); ?>
				},{
					type: "column",
					name: "Artificial Trees",
					indexLabel: "{y}",
					yValueFormatString: "$#0.##",
					showInLegend: true,
					dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
				}]
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
			 
			}
			</script>
			
			<div id="chart-<?php echo $parrent_group.'-'.$groupped_value.'-'.$onegroup ; ?>" style="height: 370px; width: 100%;"></div>
			
			<?php	
		}	
	}
}

?>

<!--<script src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>-->
<script type="text/javascript" src="https://canvasjs.com/assets/script/canvasjs.min.js"></script>


<?php




echo'<br>dataPoints<br><pre>';

print_r($dataPoints1);

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