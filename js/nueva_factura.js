
$(document).ready(function(){
	load(1);
});

function load(page){
	var moneda_id = document.getElementById('moneda').value;
	var q= $("#q").val();
	$("#loader").fadeIn('slow');
	$.ajax({
		url:'./ajax/productos_factura.php?action=ajax&page='+page+'&q='+q+'&moneda='+moneda_id,
		beforeSend: function(objeto){
			$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
		},
		success:function(data){
			$(".outer_div").html(data).fadeIn('slow');
			$('#loader').html('');

		}
	})
	moneda.onchange = function (e) {
		moneda_id = document.getElementById('moneda').value;
		var q= $("#q").val();
		$("#loader").fadeIn('slow');
		$.ajax({
			url:'./ajax/productos_factura.php?action=ajax&page='+page+'&q='+q+'&moneda='+moneda_id,
			beforeSend: function(objeto){
				$('#loader').html('<img src="./img/ajax-loader.gif"> Cargando...');
			},
			success:function(data){
				$(".outer_div").html(data).fadeIn('slow');
				$('#loader').html('');

			}
		})
	}
	
}

function agregar (id)
{
	var precio_venta=document.getElementById('precio_venta_'+id).value;
	var cantidad=document.getElementById('cantidad_'+id).value;
	var moneda_id = document.getElementById('moneda').value;	
			//Inicia validacion
			if (isNaN(cantidad))
			{
				alert('Esto no es un numero');
				document.getElementById('cantidad_'+id).focus();
				return false;
			}
			if (isNaN(precio_venta))
			{
				alert('Esto no es un numero');
				document.getElementById('precio_venta_'+id).focus();
				return false;
			}
			//Fin validacion
			$.ajax({
				type: "POST",
				url: "./ajax/agregar_facturacion.php",
				data: "id="+id+"&precio_venta="+precio_venta+"&cantidad="+cantidad+"&moneda="+moneda_id,
				beforeSend: function(objeto){
					$("#resultados").html("Mensaje: Cargando...");
				},
				success: function(datos){
					$("#resultados").html(datos);
					if(moneda_id == 1){
						detectImpuesto();
					}else{
						detectImpuestoColon();
					}
					
				}
			});

			moneda.onchange = function (e) {
				moneda_id = document.getElementById('moneda').value;
				$.ajax({
					type: "POST",
					url: "./ajax/agregar_facturacion.php",
					data: "id="+id+"&precio_venta="+precio_venta+"&cantidad="+cantidad+"&moneda="+moneda_id,
					beforeSend: function(objeto){
						$("#resultados").html("Mensaje: Cargando...");
					},
					success: function(datos){
						$("#resultados").html(datos);
						if(moneda_id == 1){
							detectImpuesto();
						}else{
							detectImpuestoColon();
						}

					}
				});
			}

		}
		
		function eliminar (id)
		{
			var moneda_id = document.getElementById('moneda').value;

			$.ajax({
				type: "GET",
				url: "./ajax/agregar_facturacion.php",
				data: "id="+id+"&moneda="+moneda_id,
				beforeSend: function(objeto){
					$("#resultados").html("Mensaje: Cargando...");
				},
				success: function(datos){
					$("#resultados").html(datos);
					if(moneda_id == 1){
						detectImpuesto();
					}else{
						detectImpuestoColon();
					}
				}
			});
			moneda.onchange = function (e) {
				moneda_id = document.getElementById('moneda').value;
				$.ajax({
					type: "GET",
					url: "./ajax/agregar_facturacion.php",
					data: "id="+id+"&moneda="+moneda_id,
					beforeSend: function(objeto){
						$("#resultados").html("Mensaje: Cargando...");
					},
					success: function(datos){
						$("#resultados").html(datos);
						if(moneda_id == 1){
							detectImpuesto();
						}else{
							detectImpuestoColon();
						}
					}
				});
			}

		}

		function detectImpuesto(){
			var impuestoCalculo=document.getElementById('impuestoCalculo').value;
			var impuestoDolares=document.getElementById('impuestoDolares').value;
			var estadoImpuestos= document.getElementById('impuesto').value;
			var total_colones=document.getElementById('total_colones').value;
			var total_factura=document.getElementById('total_factura').value;
			var total_colonesOriginal=document.getElementById('total_colonesOriginal').value;
			var total_facturaOriginal=document.getElementById('total_facturaOriginal').value;
			var totalColones2= total_colones - impuestoCalculo;//el 2 es por exonerar
			var total_factura2= total_factura - impuestoDolares;
			if(estadoImpuestos == 1){
				console.log(impuestoCalculo);
				console.log("total factura colon " + total_facturaOriginal);
				document.getElementById('impuestoShow').value="13%";
				document.getElementById('total_colonesShow').value=total_colonesOriginal;
				document.getElementById('total_facturaShow').value=total_facturaOriginal;
			}else{
				console.log("Eso va a ser un cero...");
				console.log("totalcolones2 " + totalColones2);
				document.getElementById('total_colonesShow').value=totalColones2.toFixed(2);
				document.getElementById('total_facturaShow').value=total_factura2;
				document.getElementById('impuestoShow').value="0%";

			}
		}
		function detectImpuestoColon(){
			var impuestoCalculo=document.getElementById('impuestoCalculo').value;
			var impuestoDolares=document.getElementById('impuestoDolares').value;
			var estadoImpuestos= document.getElementById('impuesto').value;
			var total_colones=document.getElementById('total_colones').value;
			var total_factura=document.getElementById('total_factura').value;
			var total_colonesOriginal=document.getElementById('total_factura').value;
			var total_facturaOriginal=document.getElementById('total_factura').value;
			var totalColones2= total_colones - impuestoCalculo;//el 2 es por exonerar
			var total_factura2= total_factura - impuestoCalculo;
			if(estadoImpuestos == 1){
				console.log(impuestoCalculo);
				console.log("total factura colon " + total_facturaOriginal);
				document.getElementById('impuestoShow').value="13%";
				document.getElementById('total_colonesShow').value=total_colonesOriginal;
			}else{
				console.log("Eso va a ser un cero...");
				console.log("totalcolones2 " + totalColones2);
				document.getElementById('total_colonesShow').value=totalColones2.toFixed(2);
				document.getElementById('impuestoShow').value="0%";

			}
		}
		impuesto.onchange = function (e) {
			var moneda_id = document.getElementById('moneda').value;
			if(moneda_id == 1){
				detectImpuesto();
			}else{
				detectImpuestoColon();
			}

		}
		
		$("#datos_factura").submit(function(){
			var id_cliente = $("#id_cliente").val();
			var id_vendedor = $("#id_vendedor").val();
			var condiciones = $("#condiciones").val();
			var impuesto = $("#impuesto").val();
			var moneda = $("#moneda").val();
			if (id_cliente==""){
				alert("Debes seleccionar un cliente");
				$("#nombre_cliente").focus();
				return false;
			}
			VentanaCentrada('./pdf/documentos/factura_pdf.php?id_cliente='+id_cliente+'&id_vendedor='+id_vendedor+'&condiciones='+condiciones+'&impuesto='+impuesto+'&moneda='+moneda,'Factura','','1024','768','true');
		});
		
		$( "#guardar_cliente" ).submit(function( event ) {
			$('#guardar_datos').attr("disabled", true);

			var parametros = $(this).serialize();
			$.ajax({
				type: "POST",
				url: "ajax/nuevo_cliente.php",
				data: parametros,
				beforeSend: function(objeto){
					$("#resultados_ajax").html("Mensaje: Cargando...");
				},
				success: function(datos){
					$("#resultados_ajax").html(datos);
					$('#guardar_datos').attr("disabled", false);
					load(1);
				}
			});
			event.preventDefault();
		})
		
		$( "#guardar_producto" ).submit(function( event ) {
			$('#guardar_datos').attr("disabled", true);

			var parametros = $(this).serialize();
			$.ajax({
				type: "POST",
				url: "ajax/nuevo_producto.php",
				data: parametros,
				beforeSend: function(objeto){
					$("#resultados_ajax_productos").html("Mensaje: Cargando...");
				},
				success: function(datos){
					$("#resultados_ajax_productos").html(datos);
					$('#guardar_datos').attr("disabled", false);
					load(1);
				}
			});
			event.preventDefault();
		})
