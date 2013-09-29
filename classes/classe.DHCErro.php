<?php

/**
 * @package: DHCErro
 * @created: 27/11/2007
 * @Author: Daniel Henrique Cassela
 * @version: 1.0
 *
 * Gerenciar erros
 */

class DHCErro {

        /** ----------------------------------------------------------------------
         *  Propriedades
         *  ---------------------------------------------------------------------- */

		/** Guarda uma instância da classe **/
		private static $instance;

        /** ----------------------------------------------------------------------
		 *  Métodos
         *  ---------------------------------------------------------------------- */

        /**
         * Construtor
         */
        public function __construct() {

                global $log;

                $log->debug->debug("DHCErro: nova instância");

        }

        public static function halt ($errstr = '') {
                if (!$errstr) $errstr = "Servidor com problemas !!, favor tentar novamente dentro de instantes".PHP_EOL;
                die($errstr.PHP_EOL);
        }

}