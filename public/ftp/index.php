<?php
$ftp_server = env('FTP_HOST');
$ftp_user_name = env('FTP_USERNAME');
$ftp_user_pass = env('FTP_PASSWORD');
$dir = "npfiles";
// Where I need to put file
$local_file = 'ctg-problem.xlsx';

// connect and login to FTP server

$ftp_conn       = ftp_connect($ftp_server,21) or die("Could not connect to $ftp_server");
$login          = ftp_login($ftp_conn, $ftp_user_name, $ftp_user_pass);

if($login) {
   echo 'connected<br>'; 
 
  // Creating directory
  if (ftp_mkdir($ftp_conn, $dir)) {
       // Where I copy the file
      $remote_file  = "$dir/".$local_file;
      // Execute if directory created successfully
      echo " $dir Successfully created";

      // upload a file
     if (ftp_put($ftp_conn, $remote_file , $local_file, FTP_ASCII)) {
         echo "successfully uploaded $local_file\n";
         exit;
     } else {
         echo "There was a problem while uploading $local_file\n";
         exit;
     }
  }
  else {
       
      // Execute if fails to create directory
      echo "Error while creating $dir";
  }

}
ftp_close($ftp_conn);
?> 