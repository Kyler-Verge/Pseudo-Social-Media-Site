<?php
   //start session, to save user_id primarily
   session_start();
   //If on this page means they clicked logout. Set session id to empty and send to login page
   unset($_SESSION['userID']);
   unset($_SESSION['userFirstName']);
   unset($_SESSION['userLastName']);
   unset($_SESSION['userEmail']);
   unset($_SESSION['userDOB']);
   unset($_SESSION['userProgram']);
   unset($_SESSION['userAvatar']);
   unset($_SESSION['userStreetNumber']);
   unset($_SESSION['userCity']);
   unset($_SESSION['userProvince']);
   unset($_SESSION['userPostalCode']);
   unset($_SESSION['dupeEmail']);
   

   header('location:login.php');
   
?>

<!--Kyler Verge-->

<!DOCTYPE html>
<html lang="en">