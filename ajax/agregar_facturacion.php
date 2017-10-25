<?php
	/*-------------------------
	Autor: Obed Alvarado
	Web: obedalvarado.pw
	Mail: info@obedalvarado.pw
	---------------------------*/
include('is_logged.php');//Archivo verifica que el usario que intenta acceder a la URL esta logueado
$session_id= session_id();
if (isset($_POST['id'])){$id=$_POST['id']; $moneda_id = $_POST['moneda'];}
if (isset($_POST['cantidad'])){$cantidad=$_POST['cantidad'];}
if (isset($_POST['precio_venta'])){$precio_venta=$_POST['precio_venta'];}
	/* Connect To Database*/
	require_once ("../config/db.php");//Contiene las variables de configuracion para conectar a la base de datos
	require_once ("../config/conexion.php");//Contiene funcion que conecta a la base de datos
	//Archivo de funciones PHP
	include("../funciones.php");
if (!empty($id) and !empty($cantidad) and !empty($precio_venta))
{
$insert_tmp=mysqli_query($con, "INSERT INTO tmp (id_producto,cantidad_tmp,precio_tmp,session_id,moneda_tmp) VALUES ('$id','$cantidad','$precio_venta','$session_id','$moneda_id')");

}
if (isset($_GET['id']))//codigo elimina un elemento del array
{
$id_tmp=intval($_GET['id']);	
$moneda_id = $_GET['moneda'];
$delete=mysqli_query($con, "DELETE FROM tmp WHERE id_tmp='".$id_tmp."'");
}
if($moneda_id == 1){
	$simbolo_moneda="$";
	//Manejo de la factura para DOLARES
?>
<table class="table">
<tr>
	<th class='text-center'>CODIGO</th>
	<th class='text-center'>CANT.</th>
	<th>DESCRIPCION</th>
	<th class='text-right'>PRECIO UNIT.</th>
	<th class='text-right'>PRECIO TOTAL</th>
	<th></th>
</tr>
<?php
	$cambio = getVentaDolarColones();
	$sumador_total=0;
	$sql=mysqli_query($con, "select * from products, tmp where products.id_producto=tmp.id_producto and tmp.session_id='".$session_id."' and tmp.moneda_tmp=".$moneda_id);
	while ($row=mysqli_fetch_array($sql))
	{
	$id_tmp=$row["id_tmp"];
	$codigo_producto=$row['codigo_producto'];
	$cantidad=$row['cantidad_tmp'];
	$nombre_producto=$row['nombre_producto'];

	$precio_venta=$row['precio_tmp'];
	$precio_venta_f=number_format($precio_venta,2);//Formateo variables
	$precio_venta_r=str_replace(",","",$precio_venta_f);//Reemplazo las comas
	$precio_total=$precio_venta_r*$cantidad;
	$precio_total_f=number_format($precio_total,2);//Precio total formateado
	$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
	$sumador_total+=$precio_total_r;//Sumador
	
		?>
		<tr>
			<td class='text-center'><?php echo $codigo_producto;?></td>
			<td class='text-center'><?php echo $cantidad;?></td>
			<td><?php echo $nombre_producto;?></td>
			<td class='text-right'><?php echo $precio_venta_f;?></td>
			<td class='text-right'><?php echo $precio_total_f;?></td>
			<td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_tmp ?>')"><i class="glyphicon glyphicon-trash"></i></a></td>
		</tr>		
		<?php
	}
	$impuesto=get_row('perfil','impuesto', 'id_perfil', 1);
	$subtotal=number_format($sumador_total,2,'.','');
	$total_iva=($subtotal * $impuesto )/100;
	$total_iva=number_format($total_iva,2,'.','');
	$total_factura=$subtotal+$total_iva;
	$total_colones=$total_factura*$cambio;
	$total_colones_f=number_format($total_colones,2);
	$total_iva_colones = $total_iva*$cambio;
	$total_iva_colones_f = number_format($total_iva_colones,2);

?>
<tr>
	<td class='text-right' colspan=4>SUBTOTAL </td>
	<td class='text-right'><?php echo $simbolo_moneda;?><?php echo number_format($subtotal,2);?></td>
	<input type="hidden" class="form-control input-sm" name="subtotal" id="subtotal" readonly value="<?php echo $total_colones_f;?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>TIPO CAMBIO: </td>
	<td class='text-right'>¢<?php echo number_format($cambio,2);?> </td>
	<input type="hidden" class="form-control input-sm" name="tipoCambio" id="tipoCambio" readonly value="<?php echo number_format($cambio,2);?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>IMP. VENTAS: </td>
	<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="impuestoShow" id="impuestoShow" readonly value="¢<?php echo $impuesto;?>"%></td>
	<input type="hidden" class="form-control input-sm" name="impuestoCalculo" id="impuestoCalculo" readonly value="<?php echo $total_iva_colones;?>">
	<input type="hidden" class="form-control input-sm" name="impuestoDolares" id="impuestoDolares" readonly value="<?php echo $total_iva;?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>TOTAL COLONES: </td>
	<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="total_colonesShow" id="total_colonesShow" readonly value="¢<?php echo $total_colones_f;?>"> </td>
	<input type="hidden" class="form-control input-sm" name="total_colones" id="total_colones" readonly value="<?php echo $total_colones;?>">
	<input type="hidden" class="form-control input-sm" name="total_colonesOriginal" id="total_colonesOriginal" readonly value="<?php echo $total_colones;?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>TOTAL DOLARES:</td>
	<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="total_facturaShow" id="total_facturaShow" readonly value="<?php echo $simbolo_moneda;?><?php echo number_format($total_factura,2);?>"></td>
	<input type="hidden" class="form-control input-sm" name="total_factura" id="total_factura" readonly value="<?php echo $total_factura;?>">
	<input type="hidden" class="form-control input-sm" name="total_facturaOriginal" id="total_facturaOriginal" readonly value="<?php echo number_format($total_factura,2);?>">
	<td></td>
</tr>

</table>

<?php
}else{
	$simbolo_moneda="¢";
	//Manejo de la factura para COLONES
?>
<table class="table">
<tr>
	<th class='text-center'>CODIGO</th>
	<th class='text-center'>CANT.</th>
	<th>DESCRIPCION</th>
	<th class='text-right'>PRECIO UNIT.</th>
	<th class='text-right'>PRECIO TOTAL</th>
	<th></th>
</tr>
<?php
	$cambio = getVentaDolarColones();
	$sumador_total=0;
	$sql=mysqli_query($con, "select * from products, tmp where products.id_producto=tmp.id_producto and tmp.session_id='".$session_id."' and tmp.moneda_tmp=".$moneda_id);
	while ($row=mysqli_fetch_array($sql))
	{
	$id_tmp=$row["id_tmp"];
	$codigo_producto=$row['codigo_producto'];
	$cantidad=$row['cantidad_tmp'];
	$nombre_producto=$row['nombre_producto'];

	$precio_venta=$row['precio_tmp'];
	$precio_venta_f=number_format($precio_venta,2);//Formateo variables
	$precio_venta_r=str_replace(",","",$precio_venta_f);//Reemplazo las comas
	$precio_total=$precio_venta_r*$cantidad;
	$precio_total_f=number_format($precio_total,2);//Precio total formateado
	$precio_total_r=str_replace(",","",$precio_total_f);//Reemplazo las comas
	$sumador_total+=$precio_total_r;//Sumador
	
		?>
		<tr>
			<td class='text-center'><?php echo $codigo_producto;?></td>
			<td class='text-center'><?php echo $cantidad;?></td>
			<td><?php echo $nombre_producto;?></td>
			<td class='text-right'><?php echo $precio_venta_f;?></td>
			<td class='text-right'><?php echo $precio_total_f;?></td>
			<td class='text-center'><a href="#" onclick="eliminar('<?php echo $id_tmp ?>')"><i class="glyphicon glyphicon-trash"></i></a></td>
		</tr>		
		<?php
	}
	$impuesto=get_row('perfil','impuesto', 'id_perfil', 1);
	$subtotal=number_format($sumador_total,2,'.','');
	$total_iva=($subtotal * $impuesto )/100;
	$total_iva=number_format($total_iva,2,'.','');
	$total_factura=$subtotal+$total_iva;
	$total_colones=$total_factura;
	$total_colones_f=number_format($total_colones,2);
	$total_iva_colones = $total_iva;
	$total_iva_colones_f = number_format($total_iva_colones,2);

?>
<tr>
	<td class='text-right' colspan=4>SUBTOTAL </td>
	<td class='text-right'><?php echo $simbolo_moneda;?><?php echo number_format($subtotal,2);?></td>
	<input type="hidden" class="form-control input-sm" name="subtotal" id="subtotal" readonly value="<?php echo $total_colones_f;?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>IMP. VENTAS: </td>
	<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="impuestoShow" id="impuestoShow" readonly value="<?php echo $impuesto;?>%"></td>
	<input type="hidden" class="form-control input-sm" name="impuestoCalculo" id="impuestoCalculo" readonly value="<?php echo $total_iva_colones;?>">
	<input type="hidden" class="form-control input-sm" name="impuestoDolares" id="impuestoDolares" readonly value="<?php echo $total_iva;?>">
	<td></td>
</tr>
<tr>
	<td class='text-right' colspan=4>TOTAL: </td>
	<td class='text-right'><input type="text" style="border: none;padding: 0px;margin: 0px;" class='text-right' name="total_colonesShow" id="total_colonesShow" readonly value="¢<?php echo $total_colones_f;?>"> </td>
	<input type="hidden" class="form-control input-sm" name="total_colones" id="total_colones" readonly value="<?php echo $total_colones;?>">
	<input type="hidden" class="form-control input-sm" name="total_colonesOriginal" id="total_colonesOriginal" readonly value="<?php echo $total_colones;?>">
	<input type="hidden" class="form-control input-sm" name="total_factura" id="total_factura" readonly value="<?php echo $total_factura;?>">
	<td></td>
</tr>

</table>

<?php 
}
?>
