<?php 

 function logResult($word='') {
	$fp = fopen("log.txt","a");
	flock($fp, LOCK_EX) ;
	fwrite($fp,"执行日期：".strftime("%Y%m%d%H%M%S",time())."\n".$word."\n");
	flock($fp, LOCK_UN);
	fclose($fp);
	}

function curl_post($url, $post) {  
  /*  $options = array(  
        CURLOPT_RETURNTRANSFER => true,  
        CURLOPT_HEADER         => false,  
        CURLOPT_POST           => true,  
        CURLOPT_POSTFIELDS     => $post,  
    );  */
	$ch = curl_init();  
    curl_setopt($ch, CURLOPT_POST, 1);  
    curl_setopt($ch, CURLOPT_URL,$url);  
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  
    curl_setopt($ch, CURLOPT_HEADER, false); 
    ob_start();  
    curl_exec($ch);  
    if (curl_errno($ch)){
        print curl_error($ch);}
    else{
        curl_close($ch);}
    $result = ob_get_contents() ;  
    ob_end_clean();  
    return $result;  
}  
$url = "http://www.dingchangzi.net/index.php/Pay/alipayNotify.html";

$res = curl_post($url,$_POST);

if($res == 'success') {
    echo "success";
    logResult("OK");
}
else{
    logResult($res."fail");
}

//logResult(curl_post($url,$_POST));
?>