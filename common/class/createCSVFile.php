<?php

class createCSVFile {
	
	var $handle;
	var $error;
	
	function create($path) {
		$this->handle = @fopen($path, "a+");
		if (!$handle) {
			$error = "\n Erro ao tentar criar o arquivo. \n";
			return false;
		}
		return true;
	}
	
	function addLine($fields) {
		fputcsv($this->handle, $fields, ";");
	}
	
	function close() {
		fclose($this->handle);
		return true;
	}
}

?>