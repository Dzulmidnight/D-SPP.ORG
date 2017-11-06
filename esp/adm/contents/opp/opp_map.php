<?php 
require_once('../Connections/dspp.php');
mysql_select_db($database_dspp, $dspp);
?>

    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': 'AIzaSyD-9tSrke72PouQMnMX-a7eZSW0jkFMBWY'
      });
      google.charts.setOnLoadCallback(drawRegionsMap);

      function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
          ['Country', 'Organizaciones'],
         <?php 
         //$query = "SELECT pais FROM opp GROUP BY pais";
         $query = "SELECT pais, COUNT(idopp) AS 'numero' FROM opp GROUP BY pais";
         $consultar = mysql_query($query,$dspp) or die(mysql_error());

         while($informacion = mysql_fetch_assoc($consultar)){
			echo "['$informacion[pais]', $informacion[numero]],";
		}

		echo "]);";
		?>  
          /*['Germany', 200],
          ['United States', 300],
          ['Brazil', 400],
          ['Canada', 500],
          ['France', 600],
          ['RU', 700]
        ]);*/

        var options = {colorAxis: {colors: ['#27ae60', '#e67e22', '#e74c3c']}};
        /*var options = {
        	colorAxis: {values: [1, 10, 100, 1000], colors: ['green', '#D1E231', 'orange' ,'red'],},
      backgroundColor: '#81d4fa',
      datalessRegionColor: '#999',
      defaultColor: '#f5f5f5',
        };*/

        var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

        chart.draw(data, options);
      }
    </script>
    <div class="row">
		<div class="col-md-9">
			<h4>MAPA DE ORGANIZACIONES SPP CERTIFICADAS</h4>
			<div class="col-xs-12">
				<div id="regions_div" style="width: 900px; height: 400px;"></div>
			</div>
		</div>

		<div class="col-md-3" style="height: 500px;overflow: scroll;">
			<table class="table table-bordered table-striped table-condensed">
				<tr style="background-color:#4caf50;color:#ffffff">
					<th>#</th>
					<th>País</th>
					<th>Organizaciones</th>
				</tr>
				<?php 
				$query = "SELECT pais, COUNT(idopp) AS 'numero' FROM opp GROUP BY pais";
         		$consultar = mysql_query($query,$dspp) or die(mysql_error());
         		$contador = 1;
				while($informacion = mysql_fetch_assoc($consultar)){
					echo '<tr>';
						echo '<td>'.$contador.'</td>';
						echo '<td>'.$informacion['pais'].'</td>';
						echo '<td>'.$informacion['numero'].'</td>';
					echo '</tr>';
					$contador++;
				}
				 ?>
			</table>
		</div>

    </div>