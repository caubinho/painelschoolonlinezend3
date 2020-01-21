<?php

$arquivo = $_GET['f'];

$extensao = pathinfo($arquivo, PATHINFO_EXTENSION);

$ext = strtolower($extensao);

$arrayExtensao = ['jpg', 'jpeg', 'pdf', 'docx', 'doc', 'txt', 'ppsx', 'pptx', 'ppt'];

foreach ($arrayExtensao as $key) {

	if($ext !== $key){

	}else{

$root = $_SERVER['DOCUMENT_ROOT'];

$caminho = $_REQUEST['c'];

$download_path = $root ."public/".$caminho ."/";

		///echo $download_path; die;

// Aqui vale qualquer coisa, desde que seja um diretório seguro :)
define('DIR_DOWNLOAD', $download_path);

// Vou dividir em passos a criação da variável $arquivo pra ficar mais fácil de entender, mas você pode juntar tudo
$arquivo = $_GET['f'];
// Retira caracteres especiais
$arquivo = filter_var($arquivo, FILTER_SANITIZE_STRING);
// Retira qualquer ocorrência de retorno de diretório que possa existir, deixando apenas o nome do arquivo
$arquivo = basename($arquivo);

// Aqui a gente só junta o diretório com o nome do arquivo
$caminho_download = DIR_DOWNLOAD . $arquivo;

// Verificação da existência do arquivo
if (!file_exists($caminho_download))
   die('Arquivo não existe!');

header('Content-type: octet/stream');

// Indica o nome do arquivo como será "baixado". Você pode modificar e colocar qualquer nome de arquivo
header('Content-disposition: attachment; filename="'.$arquivo.'";'); 

// Indica ao navegador qual é o tamanho do arquivo
header('Content-Length: '.filesize($caminho_download));

// Busca todo o arquivo e joga o seu conteúdo para que possa ser baixado
readfile($caminho_download);

exit;

}

}

