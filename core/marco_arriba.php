<?php
	/*
	Copyright (C) 2013  John F. Arroyave Gutiérrez
						unix4you2@gmail.com

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
	*/


	/*
	Title: Seccion superior
	Ubicacion *[/core/marco_arriba.php]*.  Archivo dedicado a la diagramacion de contenidos en el encabezado de la aplicacion, incluye el menu superior horizontal

	Variables de entrada:

		NombreRAD - Nombre de la aplicacion para encabezado
		PCOSESS_LoginUsuario - Nombre de usuario que se encuentra logueado en el sistema
		PCOSESS_SesionAbierta - Bandera que indica si hay una sesion activa
		ArchivoCORE - Nombre del archivo principal que procesa todas las solicitudes

		(start code)
			if (!PCO_EsAdministrador(@$PCOSESS_LoginUsuario))
				{
					$Complemento_tablas=",".$TablasCore."usuario_menu";
					$Complemento_condicion=" AND ".$TablasCore."usuario_menu.menu=".$TablasCore."menu.id AND ".$TablasCore."usuario_menu.usuario='$PCOSESS_LoginUsuario'";  // AND nivel>0
				}
			SELECT * FROM ".$TablasCore."menu ".$Complemento_tablas." WHERE posible_arriba ".$Complemento_condicion
		(end)

	Salida:
		Encabezado de aplicacion y menu superior disponible para el usuario activo

	Ver tambien:
		<Seccion inferior> | <Articulador>
	*/
?>

<?php
    // Incluye encabezados, estilos y demas del HEAD
    include_once("core/marco_arriba_bs.php");
?>
<body oncontextmenu="return false;" >
    
    <noscript>
      <div style="width: 22em; position: absolute; left: 50%; margin-left: -11em; color: red; background-color: white; border: 1px solid red; padding: 4px; font-family: sans-serif">
        Your web browser must have JavaScript enabled in order for this application to display correctly.
      </div>
    </noscript>

    <!--Marco oculto para generacion de formularios y elementos dinamicos anidados -->
    <div id="PCODIV_FormulariosDinamicos" style="visibility: hidden; display: none;"></div>

<!-- INICIA MARCO DE CHAT -->
<!-- <div id="main_container" style="overflow: auto;"> -->

    <div id="wrapper">
 
 
        <!-- Sidebar oculto al lado izquierdo -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav btn-xs">
                <div id="PCODIV_SeccionLateralFlotanteUsoInterno" align=right></div>
                <div id="PCODIV_SeccionLateralFlotante" align=right></div>
            </ul>
        </div>
        <!--Elemento requerido para uso de barra lateral oculta-->
        <a href="#menu-toggle" class="btn btn-default" id="menu-toggle" style="display: none; visibility:hidden;"></a>
        <!-- /#sidebar-wrapper oculto al lado izquierdo-->


		<?php
			// Incluye marcos con barras de navegacion
			include_once("core/marco_nav.php");
		?>

        <!-- CONTENIDO DE APLICACION -->
        <div id="page-wrapper">  <!-- page-content-wrapper -->
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        
						<?php
							//Agrega un enter minimo para las paginas si hay sesion activa
							if ($PCOSESS_SesionAbierta)
								echo '<br>';
						?>
						<div id="PCODIV_ArribaMenuSuperior"></div>
						<?php
							//Presenta advertencia sobre el modo de depuracion.  Se asume que debe estar siempre apagado en produccion
							if ($ModoDepuracion)
								mensaje($MULTILANG_ModoDepuracion, "", '', 'fa fa-fw fa-2x fa-info-circle texto-blink', 'alert alert-dismissible alert-danger');
						?>

<?php 
	//Incluye formularios de uso comun para transporte de datos
	include_once("core/marco_forms.php");

	//Despliega marcos de administracion a ser activados por el menu superior
	if (PCO_EsAdministrador(@$PCOSESS_LoginUsuario) && $PCOSESS_SesionAbierta)
		{
			include_once("core/marco_dev.php");
			include_once("core/marco_conf.php");
			include_once("core/marco_wscfg.php");
			include_once("core/marco_oauth.php");
			include_once("core/marco_param.php");
		}
	
	//Carga marco de chat solamente si esta habilitado
	if (isset($Activar_ModuloChat) && $Activar_ModuloChat>0 && @$_SESSION['username']!="")
		include_once("core/marco_chat.php");
?>


				<?php
					if ($PCOSESS_SesionAbierta && $PCO_Accion=="Ver_menu") {
						echo '<ul class="nav nav-pills btn-xs">';
						// Carga las opciones del menu superior

						// Si el usuario es diferente al administrador agrega condiciones al query
						if (!PCO_EsAdministrador(@$PCOSESS_LoginUsuario))
							{
								$Complemento_tablas=",".$TablasCore."usuario_menu";
								$Complemento_condicion=" AND ".$TablasCore."usuario_menu.menu=".$TablasCore."menu.id AND ".$TablasCore."usuario_menu.usuario='$PCOSESS_LoginUsuario'";  // AND nivel>0
							}
						$resultado=ejecutar_sql("SELECT ".$TablasCore."menu.id as id,$ListaCamposSinID_menu FROM ".$TablasCore."menu ".@$Complemento_tablas." WHERE posible_arriba=1 ".@$Complemento_condicion." ORDER BY peso");

						while($registro = $resultado->fetch())
							{
								echo '<li role="presentation">';
									echo '<form action="'.$ArchivoCORE.'" method="post" name="top_'.$registro["id"].'" id="top_'.$registro["id"].'" style="display:inline; height: 0px; border-width: 0px; width: 0px; padding: 0; margin: 0;">';
										// Verifica si se trata de un comando interno o personal y crea formulario y enlace correspondiente (ambos funcionan igual)
										if ($registro["tipo_comando"]=="Interno" || $registro["tipo_comando"]=="Personal")
											{
												echo '<input type="hidden" name="PCO_Accion" value="'.$registro["comando"].'">';
											}
										// Verifica si se trata de una opcion para cargar un objeto de practico
										if ($registro["tipo_comando"]=="Objeto")
											{
												echo'<input type="hidden" name="PCO_Accion" value="cargar_objeto">
													 <input type="hidden" name="objeto" value="'.$registro["comando"].'">';
											}
									echo '</form>';
									
									//Si tiene una URL trata la opcion como enlace estandar, sino como opcion de menu especial
									if ($registro["url"]!="")
										echo '<a title="'.PCO_ReemplazarVariablesPHPEnCadena($registro["texto"]).'" href="'.$registro["url"].'" target="'.$registro["destino"].'">';
									else
										echo '<a href="javascript:document.top_'.$registro["id"].'.submit();">';

											//Determina si la opcion es una imagen o no
											$PCO_EsImagen=0;
											if (strpos($registro["imagen"],".png") || strpos($registro["imagen"],".jpg") || strpos($registro["imagen"],".gif"))
												$PCO_EsImagen=1;
											//Si no detecta ninguna extension de archivo de imagen entonces pone boton en bootstrap
											if (!$PCO_EsImagen)
												echo '<button class="btn-circle btn-info btn-xs">
												<i class="'.$registro["imagen"].'"></i>
												</button> '.PCO_ReemplazarVariablesPHPEnCadena($registro["texto"]);
											else
												echo '<img src="'.$registro["imagen"].'" border="0" />';
									
									echo '</a>';
                                echo '</li>';
							}
                        echo '</ul>';
					}

                    // Si el usuario es administrador valida que ya haya cambiado al menos su correo
                    if (PCO_EsAdministrador(@$PCOSESS_LoginUsuario) && @$Presentar_FullScreen!=1 && $PCO_Accion=="Ver_menu")
                        {
                            $registro_usuario=ejecutar_sql("SELECT correo FROM ".$TablasCore."usuario WHERE login=? ","$PCOSESS_LoginUsuario")->fetch();
                            if ($registro_usuario["correo"]=="sucorreo@dominio.com" || $registro_usuario["correo"]=="unix4you2@gmail.com")
                                mensaje($MULTILANG_Importante, $MULTILANG_UsrActualizarAdmin, '', 'fa fa-bell fa-3x', 'alert alert-danger alert-dismissible');
                        }

				?>
	<div id="PCODIV_AbajoMenuSuperior"></div>

	<!-- INICIO  DE CONTENIDOS DE APLICACION DISENADA POR EL USUARIO -->