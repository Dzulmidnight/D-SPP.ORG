<table class="table table-bordered" id="tablaProductos">
          <tr>
            <td>#</td>
            <td>Producto</td>
            <td>Volumen Total Estimado a Comercializar</td>
            <td>Volumen como Producto Terminado</td>
            <td>Volumen como Materia Prima</td>
            <td>País(es) de Origen</td>
            <td>País(es) Destino</td> 
            <td>
              <button type="button" onclick="tablaProductos()" class="btn btn-primary" aria-label="Left Align">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
              </button>
              
            </td>   
          </tr>
          <?php 
          $query_producto_detalle = "SELECT * FROM productos WHERE idsolicitud_registro = $idsolicitud_registro";
          $producto_detalle = mysql_query($query_producto_detalle, $dspp) or die(mysql_error());
          $numeracion = 1;
          $contador = 0;
          while($row_producto = mysql_fetch_assoc($producto_detalle)){
          ?>
            <tr>
              <td>
                <?php echo $numeracion; ?>
              </td>
              <td>
                <textarea class="form-control" name="producto_actual[]" id="exampleInputEmail1" ><?php echo $row_producto['producto']; ?></textarea>
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_estimado_actual[]" id="exampleInputEmail1" placeholder="Volumen Estimado" value="<?echo $row_producto['volumen_estimado']?>">
              </td>
        
              <td>
                <input type="text" class="form-control" name="volumen_terminado_actual[]" id="exampleInputEmail1" placeholder="Volumen Terminado" value="<?echo $row_producto['volumen_terminado']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="volumen_materia_actual[]" id="exampleInputEmail1" placeholder="Volumen Materia" value="<?echo $row_producto['volumen_materia']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="origen_actual[]" id="exampleInputEmail1" placeholder="Origen" value="<?echo $row_producto['origen']?>">
              </td>
              <td>
                <input type="text" class="form-control" name="destino_actual[]" id="exampleInputEmail1" placeholder="Destino" value="<?echo $row_producto['destino']?>">
              </td>
              <td>
                <label for="eliminar<?php echo $contador; ?>">
                  <input type="checkbox" id="eliminar<?php echo $contador; ?>" name="eliminar<?php echo $contador; ?>" value="1">Eliminar
                </label>
              </td>

                <input type="hidden" name="idproducto[]" value="<?echo $row_producto['idproducto']?>">                     
            </tr>
          <?php 
          $contador++;
          $numeracion++;
          }
          ?>        
          <tr>
            <td colspan="8">
              <h6><sup>6</sup> La información proporcionada en esta sección será tratada con plena confidencialidad. Favor de insertar filas adicionales de ser necesario.</h6>
            </td>
          </tr>
</table>
