<?php 
	include("include/headers.php");
	require("include/funcionesDB.php");
	require("include/boCheckLogin.php");
?>
<html>
<head>
<?php include("include/title_meta.php"); ?>
<!-- Contact Form CSS files -->
<link type='text/css' href='css/tdil_bo.css' rel='stylesheet' media='screen' />
<?php include("include/headerBO.php"); ?>
<style type="text/css" title="currentStyle">
			@import "css/demo_page.css";
			@import "css/demo_table.css";
			@import "css/TableTools.css";
		</style>
		<script type="text/javascript" src="js/jquery.dataTables.js"></script>
			<script type="text/javascript" charset="utf-8" src="js/ZeroClipboard.js"></script>
		<script type="text/javascript" charset="utf-8" src="js/TableTools.min.js"></script>
<style>
	#content #page {
	width: 850px;
	padding: 0px;
	margin-top: 20px;
}
</style>	
<script>
var oTable = null;
function doSearch() {
	if (oTable != null) {
		oTable.fnDestroy();
	}
 	var sParticipation = document.getElementById('sParticipation').value;
 	var sGender = document.getElementById('sGender').value;
 	var sCoupleOrigin = document.getElementById('sCoupleOrigin').value;
 	var sCoupleDest = document.getElementById('sCoupleDest').value;
	oTable = $('#example').dataTable( {
					"bProcessing": true,
					"sAjaxSource": "doApp2Search.php",
					 "fnServerParams": function ( aoData ) {
				            aoData.push( { "name" : "sParticipation","value" : sParticipation },
				            			{ "name" : "sGender","value" : sGender },
				            			{ "name" : "sCoupleOrigin","value" : sCoupleOrigin },
				            			{ "name" : "sCoupleDest","value" : sCoupleDest } );
				        },
						"sDom": 'T<"clear">lfrtip',
						"oTableTools": {
							"sSwfPath": "swf/copy_cvs_xls_pdf.swf",
							"aButtons": [
								"csv",
								"xls",
								{
									"sExtends": "pdf",
									"sPdfOrientation": "landscape",
									"sPdfMessage": "Consulta de app 10."
								},
								"print"
							]
						}
				} );
			}

</script>
</head>
<body id="dt_example">

<div id="content">
	<div id="header"></div>
    <div id="block">
		<div id="promoTitle"><h1>Bienvenido al BackOffice de la promoci&oacute;n <span class="remarcado">ARLISTAN De a 2.</span></h1></div>
        <div id="hello">Hola <span class="remarcado"><?php echo($_SESSION['boNombre']);?> <?php echo($_SESSION['boApellido']);?></span></div>
        <div id="portaMenu"><?php include("include/menuBO.php"); ?></div>
        <div id="page">
            <div align="center">
            	<h2>Busquedas</h2>
                <table width="350" cellspacing="10" cellpadding="0" align="center" border="0">
                    <tr>	
                        <td>Fans de la pagina:</td>
                        <td><select id="sParticipation">
                                <option value="-1">Todos</option>
                                <option value="1">Fans</option>
                                <option value="0">No fans</option>
                            </select>
                        </td>
                    </tr>
                    <tr>	
                        <td>Sexo:</td>
                        <td><select id="sGender">
                                <option value="all">Todos</option>
                                <option value="male">Hombre</option>
                                <option value="female">Mujer</option>
                            </select>
                        </td>
                    </tr>
                    <tr>	
                        <td>Origen de pareja:</td>
                        <td><select id="sCoupleOrigin">
                                <option value="-1">Todos</option>
                                <option value="1">Origen</option>
                                <option value="0">No origen</option>
                            </select>
                        </td>
                    </tr>
                    <tr>	
                        <td>Destino de pareja:</td>
                        <td><select id="sCoupleDest">
                                <option value="-1">Todos</option>
                                <option value="1">Destino</option>
                                <option value="0">No destino</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" align="center">
                            <input type="button" onClick="doSearch();" value="Buscar">
                        </td>
                    </tr>
                </table>			
            </div>
            <div id="container">
                <div id="dynamic">
                    <table cellpadding="0" cellspacing="0" border="0" class="display" id="example">
                        <thead>
                            <tr>
                                <th width="5%">FB id</th>
                                <th width="25%">FB nombre</th>
                                <th width="25%">FB usuario</th>
                                <th width="25%">FB sexo</th>
                                <th width="25%">Origen</th>
                                <th width="25%">Destino</th>
                                <th width="25%">Amigo al que se unio</th>
                                <th width="25%">Amigo que se unio</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th width="5%">FB id</th>
                                <th width="25%">FB nombre</th>
                                <th width="25%">FB usuario</th>
                                <th width="25%">FB sexo</th>
                                <th width="25%">Origen</th>
                                <th width="25%">Destino</th>
                                <th width="25%">Amigo al que se unio</th>
                                <th width="25%">Amigo que se unio</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>
</body>
</html>