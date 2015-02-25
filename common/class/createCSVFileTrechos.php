<?php

class createCSVFileTrechos extends createCSVFile {
	
	function addTitle() {
		$titulo = array("identificação única", "responsável", "argumento", "código", "número documento", "número parágrafo", "trecho");
		$this->addLine($titulo);
	}
	
	function treatArray($entrada, $codigos, $codigos2, $paragrafo) {
		if (!(preg_match('/<\/archives\/textos\//', $paragrafo) || preg_match('/<http:\/\/www1\.folha\.uol\.com\.br/', $paragrafo))) {
			$aux_parag = explode(":", $entrada[3]);
			if (sizeof(@$codigos2) > 0) {
				for ($i = 0; $i < sizeof($codigos2); $i++) {
					$line = array($entrada[2], $entrada[6], $codigos[$i], $codigos2[$i], $aux_parag[0], $aux_parag[1], $paragrafo);
					$this->addLine($line);
				}
			} else
				$this->addLine(array($entrada[2], $entrada[6], "", "", $aux_parag[0], $aux_parag[1], $paragrafo));
			
			return true;
		}
	}
	
}

?>