<?php
$ini_file = php_ini_loaded_file();
$cert_path = "C:\\Users\\MSI\\Documents\\Flutter\\sm_agro_final_project\\sm_agro_laravel\\storage\\app\\cacert.pem";
$append = "\n[curl]\ncurl.cainfo=\"$cert_path\"\n[openssl]\nopenssl.cafile=\"$cert_path\"\n";

file_put_contents($ini_file, $append, FILE_APPEND);
echo "Successfully updated php.ini!";
