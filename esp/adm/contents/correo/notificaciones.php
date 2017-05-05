<?php 

 ?>

 	<div class="col-md-12">

		<form class="form-inline">
			<div class="checkbox" style="margin-right:10px;">
				<label>
					<input type="checkbox"> Todos
				</label>
			</div>
			<div class="checkbox" style="margin-right:10px;">
				<label>
					<input type="checkbox"> OPP(s)
				</label>
			</div>
			<div class="checkbox" style="margin-right:10px;">
				<label>
					<input type="checkbox"> Empresas
				</label>
			</div>
			<div class="checkbox" style="margin-right:10px;">
				<label>
					<input type="checkbox"> Administradores
				</label>
			</div>
		</form>

 	</div>

<div class="col-md-12">
	<textarea class="editor_texto" name="" id="" cols="30" rows="10"></textarea>
</div>

<?php 
while($oc = mysql_fetch_assoc($row_oc)){
	echo $oc['spp'].'<br>';
}
 ?>