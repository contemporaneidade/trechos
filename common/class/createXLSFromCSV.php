<?php

class createXLSFromCSV {
	
	var $Creator;
	var $LastModifiedBy;
	var $Title;
	var $Subject;
	var $Description;
	var $Keywords;
	var $Category;
	
	function create($csv_file, $xls_file) {
		$alfabeto = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 
							'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V',
							'W', 'X', 'Y', 'Z');
		
		$objPHPExcel = new PHPExcel();
		
		$objPHPExcel->getProperties()->setCreator($Creator)
							 ->setLastModifiedBy($LastModifiedBy)
							 ->setTitle($Title)
							 ->setSubject($Subject)
							 ->setDescription($Description)
							 ->setKeywords($Keywords)
							 ->setCategory($Category);
	
		$handle = fopen($csv_file, "r");
		if ($handle) {
			$i = 1;
			while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
				for ($j = 0; $j < sizeof($data); $j++) {
					$celula = $alfabeto[$j].$i;
					$objPHPExcel->setActiveSheetIndex(0)->setCellValue($celula, $data[$j]);
				}
				$i++;
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('output');
		$objPHPExcel->setActiveSheetIndex(0);
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		$objWriter->save(str_replace('.php', '.xls', __FILE__));
	}
	
}

?>