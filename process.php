<?php

/*

Encrypts given data with given method and key, returns a raw or base64 encoded string

*/


require('connection.php');

$data = htmlentities($_POST['note']);
$method = "aes-256-cbc";
$key = openssl_random_pseudo_bytes(openssl_cipher_iv_length($method));
$urlhash = hash('sha256', '"'.microtime().'-'.' th15 is kind4 5a1ty :3'.mt_rand(0,9999).'~'.mt_rand(0,9999).'^'.mt_rand(0,9999).'#'.mt_rand(0,9999).'%'.sha1(md5(mt_rand(0,9999)).mt_rand(0,9999)).';\_');

//encrypt data
//OPENSSL_RAW_DATA OR FALSE
$encrypted = openssl_encrypt($data,$method,$key);

//insert into database
$query = mysqli_query($conn,"INSERT INTO privnotes
  SET cryptkey='".mysqli_real_escape_string($conn,$key)."',
      note='".mysqli_real_escape_string($conn,$encrypted)."',
      url='".mysqli_real_escape_string($conn,$urlhash)."'
      ") or die(mysqli_error($conn));

if($query){
  echo $urlhash;
} else {
  echo "Database Error, email this@mafkr.com";
}


/*

OTHER ENCRYPTION METHODS


$plaintext = 'Testing OpenSSL Functions';
$methods = openssl_get_cipher_methods();
//$clefSecrete = 'flight';
echo '<pre>';
foreach ($methods as $method) {
    $ivlen = openssl_cipher_iv_length($method);
    $clefSecrete = openssl_random_pseudo_bytes($ivlen);
    $iv = openssl_random_pseudo_bytes($ivlen);

    $encrypted = openssl_encrypt($plaintext, $method, $clefSecrete, OPENSSL_RAW_DATA, $iv);
    $decrypted = openssl_decrypt($encrypted, $method, $clefSecrete, OPENSSL_RAW_DATA, $iv);
    echo 'plaintext='.$plaintext. "\n";
    echo 'cipher='.$method. "\n";
    echo 'encrypted to: '.$encrypted. "\n";
    echo 'decrypted to: '.$decrypted. "\n\n";
}
echo '</pre>';
*/


?>
