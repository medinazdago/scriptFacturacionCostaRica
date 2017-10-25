<?php
session_start();
if (!isset($_SESSION['user_login_status']) AND $_SESSION['user_login_status'] != 1) {
	header("location: ../../login.php");
	exit;
}
/* Connect To Database*/
include("../../config/db.php");
include("../../config/conexion.php");
	//Archivo de funciones PHP
include("../../funciones.php");
//Incluimos la clase de PHPMailer
require_once('../../libraries/phpmailer/class.phpmailer.php');
$id_factura= intval($_GET['id_factura']);
$email= $_GET['email'];
$sql_count=mysqli_query($con,"select * from facturas where id_factura='".$id_factura."'");
$count=mysqli_num_rows($sql_count);
if ($count==0)
{
	echo "<script>alert('Factura no encontrada')</script>";
	echo "<script>window.close();</script>";
	exit;
}
$sql_factura=mysqli_query($con,"select * from facturas where id_factura='".$id_factura."'");
$rw_factura=mysqli_fetch_array($sql_factura);
$numero_factura=$rw_factura['numero_factura'];
$id_cliente=$rw_factura['id_cliente'];
$id_vendedor=$rw_factura['id_vendedor'];
$fecha_factura=$rw_factura['fecha_factura'];
$condiciones=$rw_factura['condiciones'];
$simbolo_moneda=get_row('perfil','moneda', 'id_perfil', 1);
require_once(dirname(__FILE__).'/../html2pdf.class.php');
    // get the HTML
ob_start();
include(dirname('__FILE__').'/res/ver_factura_html.php');
$content = ob_get_clean();

try
{
        // init HTML2PDF
	$html2pdf = new HTML2PDF('P', 'LETTER', 'es', true, 'UTF-8', array(0, 0, 0, 0));
        // display the full page
	$html2pdf->pdf->SetDisplayMode('fullpage');
        // convert
	$html2pdf->writeHTML($content, isset($_GET['vuehtml']));
        // send the PDF
	$html2pdf->Output('../../pdfTemporal/factura'.$numero_factura.'.pdf', 'F');
}
catch(HTML2PDF_exception $e) {
	echo $e;
	exit;
}

$mensajeDefault="Muchas gracias por su preferencia, adjuntamos la factura digital de su compra en este correo.";

function msjDefault($msjDefault){
if(isset($_POST['message'])AND($msjDefault != $_POST['message'])){
	echo $_POST['message'];
} else {
	echo $msjDefault;
}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	<title>Enviar por email</title>
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
	
	<link rel="stylesheet" href="css/custom.css">
	<link rel=icon href='img/logo-icon.png' sizes="32x32" type="image/png">
</head>
<body> 
	<div class="container">
		<div class="panel panel-info">
			<div class="panel-heading">
				<h4><i class='glyphicon glyphicon-envelope'></i> Enviar email </h4>
			</div>
			<div class="panel-body">

				<form class="form-horizontal" method="post">
					<div class="form-group">
						<label for="email" class="col-sm-3 control-label">Email del cliente:</label>
						<div class="col-sm-8">
							<input class="form-control input-sm" type="text" name="email" value="<?php echo $email; ?>" readonly>
						</div>
					</div>
					<div class="form-group">
						<label for="asunto" class="col-sm-3 control-label">Asunto</label>
						<div class="col-sm-8">
							<input class="form-control input-sm" type="text" name="asunto" value="Factura <?php echo $numero_factura; ?> - Gracias por su preferencia">
						</div>
					</div>
					<div class="form-group">
						<label for="mensaje" class="col-sm-3 control-label">Mensaje</label>
						<div class="col-sm-8">
						<textarea class="form-control input-sm" rows="5" name="message" cols="30"><?php msjDefault($mensajeDefault); ?></textarea>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" onclick="self.close()">Cerrar</button>
						<input type="submit" name="submit" value="Enviar correo" class="btn btn-primary">
					</div>

				</form>


			</div>
		</div>	
		
	</div>
	<hr>
</body>
</html>

<?php 

if(isset($_POST['submit'])){
	$subject = $_POST['asunto'];
	$body = $_POST['message'];
	$my_name = "Facturación Digital Simple";//Agregue su nombre o asunto
	$my_mail = "demo@demo.com";//Agregue su propio email 
	$my_replyto = "dagoberto@demo.com";//El email para respuestas
	$my_file = 'factura'.$numero_factura.'.pdf';
	$file = "../../pdfTemporal/".$my_file;
	$filename = 'factura-'.$numero_factura.'.pdf';
	$correo = new PHPMailer(); //Creamos una instancia en lugar usar mail()

//Usamos el SetFrom para decirle al script quien envia el correo
	$correo->SetFrom($my_mail, $my_name);

//Usamos el AddReplyTo para decirle al script a quien tiene que responder el correo
	$correo->AddReplyTo($my_replyto,$my_name);

//Usamos el AddAddress para agregar un destinatario
	$correo->AddAddress($email, $email);

//Ponemos el asunto del mensaje
	$correo->Subject = $subject;

/*
 * Si deseamos enviar un correo con formato HTML utilizaremos MsgHTML:
 * $correo->MsgHTML("<strong>Mi Mensaje en HTML</strong>");
 * Si deseamos enviarlo en texto plano, haremos lo siguiente:
 * $correo->IsHTML(false);
 * $correo->Body = "Mi mensaje en Texto Plano";
 */
$correo->MsgHTML($body);

//Si deseamos agregar un archivo adjunto utilizamos AddAttachment
$correo->AddAttachment($file );

//Enviamos el correo
if(!$correo->Send()) {
	$resultado = "Error enviando el correo, por favor intente de nuevo.  Error:". $correo->ErrorInfo;
	echo '<div id="resultados"><div class="alert alert-warning alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
	<strong>Error!</strong> '.$resultado.'</div></div>';
} else {
	$resultado = "Correo enviado con éxito!!";
	echo '<div id="resultados"><div class="alert alert-success alert-dismissible" role="alert">
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
	<strong>Aviso!</strong> '.$resultado.'</div></div>';
}


}
?>
