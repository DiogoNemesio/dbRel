<?php

/**
 * Desenhar a tela de Login
 * 
 * @package: DHCLayoutLogin
 * @created: 20/12/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 * 
 */

class DHCLayoutLogin  {
	
	private $ui;

	public function __construct() {
	}
	
	public function setLoadAction() {
		$submit 	= $this->ui->getWidget('LoginButton');
		
		$submit->process();
		

		if ($submit->hasBeenClicked()) sleep(1);
	}

	public function setUI (SwatUI $ui) {
		$this->ui	= $ui;
	}

	public function setAction ($url) {
		$form			= $this->ui->getWidget('SFormLogin');
		$form->action	= $url;
		
	}
    
	public function setMessage ($mensagem = '') {
		$mDisplay		= $this->ui->getWidget('login_message');
		
		if ($mensagem) {
			 $message = new SwatMessage($mensagem,SwatMessage::WARNING);
			 $mDisplay->add($message);
		}
	}
}