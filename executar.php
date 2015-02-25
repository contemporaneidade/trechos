<?php

require "common/config.php";
require "common/class/createCSVFile.php";
require "common/class/createCSVFileTrechos.php";
require "common/class/createXLSFromCSV.php";
require 'common/PHPExcel/Classes/PHPExcel.php';
require 'common/PHPExcel/Classes/PHPExcel/IOFactory.php';	

if ($argc != 2)
	die("\n Uso correto: php executar.php [nome do arquivo] \n");

$file = "".$path_entrada.$argv[1];

$tmp_arr = explode('.', strtoupper($argv[1]));
if ($tmp_arr[sizeof($tmp_arr)-1] != "TXT")
	die("\n A extensao do arquivo fornecido nao foi reconhecida. \n");

$handle = @fopen($file, "r");
if ($handle) {
	
	echo "\n Processo inciado: ".date("d-m-Y H:i:s")."\n";
	
	$tmp_file = $path_temp."tmp_".date("Ymd_His").".csv";
	$xls_file = $tmp_arr[0].".xls";
	
	//Criando a instância do arquivo CSV
	$csv = new createCSVFileTrechos;
	
	$csv->create($tmp_file);
	$csv->addTitle();
	
	//P 1: GLB - 2001-02-04.rtf - 1:2 [Data de Publicação: Domingo 4 ..]  (2:2)   (Larissa)
	//P183: GLB - 2003-02-23 4.rtf - 183:1 [Daqui a alguns anos a Uerj ser..]  (14:14)   (Luiz)
	$pattern[0] = '/([a-zA-Z][ ]?[0-9]*): ([a-zA-Z]{1,3} - [0-9]{4}-[0-9]{2}-[0-9]{2}.*\.rtf) - ([0-9]*:[0-9]*) \[(.*)\]  \(([0-9]*:[0-9]*)\)   \((.*)\)/';
	
	//c01. AAR... - Family: c1) AAR...
	//c18. AAR pode estigmatizar os beneficiários - Family: c3) Ineficiência, incompletude ou paliatividade da AAR] [c26. AAR não leva em conta o mérito - Family: c4) AAR põe em perigo o mérito e qualidade do ensino 
	//$pattern[1] = '/([cfCF][0-9]{2})\. ([a-zA-Z0-9]*) - /';
	//$pattern[1] = '/([cfCF][0-9]{2})/';
	$pattern[1] = '/([cfCF][0-9]{2}\. [ ,a-zA-Z, 0-9, ãÃ, áÁ, àÀ, âÂ, êÊ, éÉ, íÍ, úÚ, üÜ, õÔ, ôÔ, çÇ]*)/';
	$pattern[2] = '/([cfCF][0-9]{2})/';
	
	$open_ent = false;
	$open_cod = false;
	$open_mem = false;
	$tot_mem = 0;
	
	$cont = 0;
	while (!feof($handle)) {
		$buffer = fgets($handle, 4096);
		
		if (!$open_ent) {
			if (preg_match($pattern[0], $buffer, $arr_ent))
				$open_ent = true;
		}
		
		if ($open_ent && !$open_cod) {
			if (preg_match('/No codes/', $buffer) || preg_match('/Codes:/', $buffer)) {
				if (preg_match('/Codes:/', $buffer)) {
					$open_cod = true;
					$arr_cod = array();
					$arr_cod2 = array();
					
					$tmp_cod = explode("] [", $buffer);
					for ($i = 0; $i < sizeof($tmp_cod); $i++) {
						if (preg_match($pattern[1], $tmp_cod[$i], $tmp_cod2))
							$arr_cod[] = $tmp_cod2[1];
							
						if (preg_match($pattern[2], $tmp_cod[$i], $tmp_cod2))
							$arr_cod2[] = $tmp_cod2[1];
					}
				}
			}
		}
		
		if (!$open_mem) {
			if (preg_match('/No memos/', $buffer) || preg_match('/Memos:/', $buffer))
				$open_mem = true;
		}
		
		if ($open_mem) {
			$tot_mem++;
			if ((int)$tot_mem > 2) {
				$open_mem = false;
				$tot_mem = 0;
				
				$open_ent = false;
				$open_cod = false;
				
				//print_r($arr_ent);
				//print_r($tmp_cod);
				//print_r($arr_cod);
				
				$csv->treatArray($arr_ent, $arr_cod, $arr_cod2, trim($buffer));
				
				unset($arr_ent);
				unset($tmp_cod);
				unset($tmp_cod2);
				unset($arr_cod);
				unset($arr_cod2);
			}
		}
		
		$cont++;
		if ($cont > 100) {
			echo ".";
			$cont = 0;
		}	
	}
	$csv->close();
	fclose($handle);
	
	echo "\n Arquivo temporario criado: ".$tmp_file."\n";
} else
	die("\n Problema ao abrir arquivo. Caminho incorreto. O arquivo deveria estar na pasta: $path_entrada \n");

echo "\n Criando arquivo do excel \n";

$xls = new createXLSFromCSV;

$xls->Creator = $creator;
$xls->LastModifiedBy = $lastModifiedBy;
$xls->Title = $title;
$xls->Subject = $subject;
$xls->Description = $description;
$xls->Keywords = $keywords;
$xls->Category = $category;

$xls->create($tmp_file, $path_saida.$xls_file);

rename("common/class/createXLSFromCSV.xls", "common/class/".$xls_file);
if (copy("common/class/".$xls_file, $path_saida.$xls_file))
	unlink("common/class/".$xls_file);

echo "\n Arquivo do excel criado: $xls_file \n";

echo "\n Processo finalizado: ".date("d-m-Y H:i:s")."\n";