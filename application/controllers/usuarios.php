<?php
	class Usuarios extends CI_Controller{
		
		private $errores = array(
			'usuex'=>"E01001: Nombre de usuario ya registrado",
			'usuv'=>"Debe escribir un nombre de usuario",
			'clavev'=>"E01003: Debe escribir una clave",
			'clavec'=>"E01004: Clave demasiado corta",
			'usuiv'=>"E01005: Usuario no valido",
			'sessnv'=>'E01006: No hay sesion iniciada',
			'nousf'=>'E01007: No se encontraron resultados',
			'nopf'=>'E01008: No se encontraron perfiles'
		);
		
		public function __construct(){
			parent::__construct();
			$this->load->model('UsuariosModel');
		}
		
		public function existeUsuario(){
			$usuario = trim($this->input->post('nickname'));
			$existe = $this->UsuariosModel->existeUsuario('nickname',$usuario);
			return $existe;
		}
		
		private function validaClave(){
			$valida = false;
			$clave = trim($this->input->post('clave'));
			if(strcasecmp($clave,"")==0) echo $this->errores['clavev'];
			else{
				if(strlen($clave)<=4) echo $this->errores['clavec'];
				else $valida = true;
			}
			return $valida;
		}
		
		public function crearUsuario(){
			$mensaje = "";
			$usuario = trim($this->input->post('nickname'));
			if(strcasecmp($usuario,"")!=0){
				if($this->existeUsuario()){
					$mensaje = $this->errores['usuex'];
				}
				else{
					$clave = trim($this->input->post('clave'));
					if($this->validaClave($clave)){
						$tipo = 1;
						$banco = 1;
						if($this->input->post('tipo')!=null) $tipo = $this->input->post('tipo');
						if($this->input->post('banco')!=null) $banco = $this->input->post('banco');
						$datos = array(
							'nickname'=>$usuario, 'clave'=>md5($clave),'tipo'=>trim($tipo),
							'nombres'=>trim($this->input->post('nombre')), 'apellidos'=>trim($this->input->post('apellido')),
							'celular'=>trim($this->input->post('celular')), 'email'=>trim($this->input->post('email')),
							'idbanco'=>trim($banco),'numerocuenta'=>trim($this->input->post('numerocuenta'))
						);
						$mensaje = $this->UsuariosModel->crearUsuario($datos);
					}
				}
			}
			else{
				$mensaje = $this->errores['usuv'];
			}
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function borrarUsuario(){
			$mensaje = "";
			$usuario = trim($this->input->post('nickname'));
			if($this->existeUsuario($usuario)){
				$mensaje = $this->UsuariosModel->borrarUsuario($usuario);
			}
			else{
				$mensaje = $this->errores['usuiv'];
			}
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function modificarUsuario(){
			$mensaje = "";
			$idusuario = $this->input->post('idusuario');
			$datos = array(
				'nombres'=>trim($this->input->post('nombre')), 'apellidos'=>trim($this->input->post('apellido')), 
				'celular'=>trim($this->input->post('celular')), 'email'=>trim($this->input->post('email'))
			);
			$mensaje = $this->UsuariosModel->modificarUsuario($idusuario,$datos);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function datosUsuario(){
			$mensaje = '';
			$valor = trim($this->input->post('nickname'));
			$msg = $this->UsuariosModel->buscarUsuarios("nickname",$valor);
			if(strcasecmp($msg,"")==0) $mensaje = '{"error":"'.$this->errores['nousf'].'"}';
			else $mensaje = '{"usuarios":['.$msg.']}';
			echo $mensaje;
		}
		
		public function buscarUsuarios(){
			$mensaje = '';
			$columna = trim($this->input->post('col'));
			$valor = trim($this->input->post('val'));
			$msg = $this->UsuariosModel->buscarUsuarios($columna,$valor);
			if(strcasecmp($msg,"")==0) $mensaje = '{"error":"'.$this->errores['nousf'].'"}';
			else $mensaje = '{"usuarios":['.$msg.']}';
			echo $mensaje;
		}
		
		public function panelUsuario(){
			$nickname = $this->input->post('nickname');
			$u = $this->UsuariosModel->buscarUsuarios("nickname",$nickname);
			$u = '['.$u.']';
			$usr = json_decode($u);
			$usuario = $usr[0];
			$datos = '{"datos":[';
			switch($usuario->tipo){
				case 1:
					$datos .= '{"menu":"Perfil;Solicitudes;Soporte;Estad&iacute;sticas"}';
					break;
				case 2:
					$datos .= '{"menu":"Perfil;Solicitudes"}';
					break;
				case 3:
					$datos .= '{"menu":"Perfil;Solicitar;Mis solicitudes;Cargar saldo;Soporte"}';
					break;
			}
			$datos .= ']}';
		}
		
		public function ingresarMateriasAsesor(){
			$nickname = $this->input->post('nickname');
			$materias = $this->input->post('materias');
			$arraymaterias = explode(";",$materias);
			$mensaje = $this->UsuariosModel->ingresarMateriasAsesor($nickname,$arraymaterias);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function actualizarMateriasAsesor(){
			$nickname = $this->input->post('nickname');
			$materias = $this->input->post('materias');
			$arraymaterias = explode(";",$materias);
			$mensaje = $this->UsuariosModel->actualizarMateriasAsesor($nickname,$arraymaterias);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function calificarAsesor(){
			$idasesor = $this->input->post('idasesor');
			$puntaje = $this->input->post('puntaje');
			$mensaje = $this->UsuariosModel->calificarAsesor($idasesor,$puntaje);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function calificacionAsesor(){
			$nickname = $this->input->post('nickname');
			$mensaje = $this->UsuariosModel->calificacionAsesor($nickname);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function notificacionesNoLeidasCant(){
			$nickname = $this->input->post('nickname');
			$mensaje = $this->UsuariosModel->notificacionesNoLeidasCant($nickname);
			$resp = array("msg"=>html_entity_decode($mensaje));
			//echo $_GET['callback'].'('.json_encode($resp).')';
			echo json_encode($resp);
		}
		
		public function notificacionesNoLeidas(){
			$mensaje = '';
			$nickname = $this->input->post('nickname');
			$mensaje = $this->UsuariosModel->notificacionesNoLeidas($nickname);
			if(strcasecmp($msg,"")==0) $mensaje = '{"error":"'.$this->errores['nousf'].'"}';
			else $mensaje = '{"notificaciones":['.$msg.']}';
			echo $mensaje;
		}
		
		public function marcarLeida(){
			$id = $this->input->post('id');
			$this->UsuariosModel->marcarLeida($id);
		}
	}