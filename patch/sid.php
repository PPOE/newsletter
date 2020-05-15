<?php
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
$key ='(`Xu}ibIg"(\Qk~"kZht7&!901VJ9te:';
function base64_url_encode($input)
{
    return strtr(base64_encode($input), '+/=', '-_$');
}

function base64_url_decode($input)
{
    return base64_decode(strtr($input, '-_$', '+/='));
}

if(isset($_GET['q']) && isset($_GET['s'])){
$s = intval($_GET['s']);
$sid =  mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key , base64_url_decode($_GET['q']), MCRYPT_MODE_CBC);

echo base64_decode($_GET['q'])."<br>";
echo $sid;

}
