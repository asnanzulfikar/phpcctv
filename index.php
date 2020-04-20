<?php
error_reporting(1);
$uri_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri_segments = explode('/', $uri_path);
//$cctv_pos = $_GET['pos'];
$cctv_pos = 'jurug';
//echo $uri_segments[1];
$today = date("Ymd");
$ftp_server = "xxx.xxx.xxx.xxx";
$ftp_conn = ftp_connect($ftp_server);
$login = ftp_login($ftp_conn,"anonymous","");
$mode = ftp_pasv($ftp_conn, TRUE);

if ((!$ftp_conn) || (!$login) ||(!$mode)) {
   die("FTP connection has failed !");
}
//////////////////////////////////////////////////////////////////////////
$dir = "/cctv/".$cctv_pos."/";
$dirlist = ftp_nlist($ftp_conn, $dir);
$folder_array = [];
//filter array yang hanya sebuah folder tanggal 
foreach ($dirlist as $key) {
	$folder = explode("/",$key)[3];
	if (strlen(intval($folder)) == 8 ){
		array_push($folder_array,$folder);
	}
}

$last_folder_date = end($folder_array);
$last_folder_date = explode("/",$last_folder_date);
$get_folder_date_last = end($last_folder_date);
//////////////////////////////////////////////////////////////////////////

$dir1 = "/cctv/".$cctv_pos."/".$get_folder_date_last."/images/";
$gbr_array = [];
$dirlist1 = ftp_nlist($ftp_conn, $dir1);

//filter untuk file jpg saja
foreach ($dirlist1 as $key) {
	# code...
	$ext = explode(".",$key)[1];
	if($ext == "jpg"){
		array_push($gbr_array,$key);
	}
}

//baris kode untuk filter file jpg lebih dari 115KB yang dipastikan gambar sempurna
$gbr = array();
foreach ($gbr_array as $file) {
	$res = ftp_size($ftp_conn, $file);
	if ($res >= 115000){
		$last_filename = explode("/",$file);
		$end_last_file_name = end($last_filename);
		array_push($gbr,$end_last_file_name);
	}
}
$gambar = end($gbr);

ftp_close($ftp_conn);

$file = file_get_contents('ftp://'.$ftp_server.'/cctv/'.$cctv_pos.'/'.$get_folder_date_last.'/images/'.$gambar);
if($file){
	header('Content-Type: image/jpeg');
	echo $file;
}else{
	echo "gambar tidak ditemukan";
}

?>
