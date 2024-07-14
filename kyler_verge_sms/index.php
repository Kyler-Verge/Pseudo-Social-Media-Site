<?php
   //start session, to save user_id primarily
   session_start();
   //If on this page without a user id session value, means they are not logged in. Send to login.php
   /* if(!isset($_SESSION['userID'])){
      header('location:logout.php');
   } */

?>

<!DOCTYPE html>
<html lang="en">

<!--Kyler Verge 101114854-->

<head>
   <meta charset="utf-8">
   <title>Check Posts</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
</head>

<!--Body of HTML-->
<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>

   <!-- Left Side navbar-->
   <nav>
      <a class="active" href="index.php">Home</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Log Out</a>

      <!--If the user has the correct permission value, display user list option in nav bar-->
      <?php

      if($_SESSION['userPermission'] == 0){
         echo '<a href="user_list.php">User List</a>';
      }

      ?>

   </nav>

   <main>
      <section>
         <h2>New Post</h2>
         <form method="post" action="">
            <fieldset>

               <!--Text area for post-->
               <textarea maxlength="200" id="post_textbox" name="new_post" placeholder="What is happening? (max 200 char)"></textarea> <br>

               <!--Submit and Reset buttons-->
               <input type="submit" value="Post" name="post">
               <input type="reset"> <br>

            </fieldset>
         </form>

         <!--PHP Script-->
         <?php

         //servers info
         include("connection.php");

            if(isset($_POST["post"])){

               try{
                  $conn = new mysqli($server_name, $username, $password, $database_name);

                  //Insert post info into users_posts table
                  //$sql = 'INSERT INTO users_posts(student_id, new_post) VALUES ("'.$_SESSION["userID"].'","'.$_POST["new_post"].'");';
                  $sql = 'INSERT INTO users_posts(student_id, new_post) VALUES (?,?);';
                  $statement=$conn->prepare($sql);
                  $statement->bind_param('is',$_SESSION["userID"], $_POST["new_post"]);
                  $statement->execute();
                  //$conn->query($sql);

                  //Close connection to Database
                  $conn->close();

               }catch (mysqli_sql_exception $e){
                  $error = $e->getMessage();
                  echo $error;
               }
            }

            //Retrieve 10 most recent posts from users_posts table
            try{
               $conn = new mysqli($server_name, $username, $password, $database_name);

               //Retrieve rows from users_posts table
               $sql = 'SELECT * FROM users_posts ORDER BY post_date DESC LIMIT 10';
               $results = $conn->query($sql);

               echo "<div class='posts'>";

               //Display retrieved data from the table
               $postamount = 10;
               while($row = $results->fetch_assoc()){

                  echo "<details> <summary> POST ID: " .$row['post_id']. " POST DATE:" .$row['post_date']. "</summary><p>" .$row['new_post']. "</p></details><br>";
               }

               echo "</div>";

               //Close connection to Database
               $conn->close();

            }catch (mysqli_sql_exception $e){
               $error = $e->getMessage();
               echo $error;
            }

            //User login php script
            if(isset($_POST["login"])){

               try{

                  $conn = new mysqli($server_name, $username, $password, $database_name);

                  //Check if inputted email is already in database
                  $sql = 'SELECT * FROM users_info WHERE student_email = "'.$_POST["student_email"].'"';
                  $checkResult = $conn->query($sql);

                  if($checkResult){
                     //If the inputted email is already in database, send back to register.php, send error message and 
                     //exit the submit form.
                     if(mysqli_num_rows($checkResult) == 0){
                        $_SESSION["noEmail"] = "This email is not registered";
                        echo '<script>window.location = "login.php"</script>';
                        exit();
                     }                    
                  } 

                  //Retrieve user id with given email to get the password
                  $sql = 'SELECT student_id FROM users_info WHERE student_email = "'.$_POST["student_email"].'"';
                  $result = $conn->query($sql);
                  $student_id = $result->fetch_assoc();

                  //Use retrieved user id to acquire password linked to it
                  $sql = 'SELECT password FROM users_passwords WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $student_password = $result->fetch_assoc();


                  //Check if inputted password is same as stored hash password, if not, send back to login.php
                  if(!(password_verify($_POST['password'], $student_password['password']))){
                     $_SESSION["wrongPassword"] = "The password is incorrect";
                     echo '<script>window.location = "login.php"</script>';
                     exit();
                  }

                  //Setup all session variables
                  //user info table
                  $sql = 'SELECT * FROM users_info WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $users_info = $result->fetch_assoc();

                  $_SESSION["userID"] = $student_id['student_id'];
                  $_SESSION["userFirstName"] = $users_info['first_name'];
                  $_SESSION["userLastName"] = $users_info['last_name'];
                  $_SESSION["userEmail"] = $users_info['student_email'];
                  $_SESSION["userDOB"] = $users_info['dob'];

                  //user address table
                  $sql = 'SELECT * FROM users_address WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $users_address = $result->fetch_assoc();

                  $_SESSION['userStreetNumber'] = $users_address['street_number'];
                  $_SESSION['userStreetName'] = $users_address['street_name'];
                  $_SESSION['userCity'] = $users_address['city'];
                  $_SESSION['userProvince'] = $users_address['province'];
                  $_SESSION['userPostalCode'] = $users_address['postal_code'];

                  //user avatar table
                  $sql = 'SELECT * FROM users_avatar WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $users_avatar = $result->fetch_assoc();

                  $_SESSION['userAvatar'] = $users_avatar['avatar'];

                  //user program table
                  $sql = 'SELECT * FROM users_program WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $users_program = $result->fetch_assoc();

                  $_SESSION['userProgram'] = $users_program['program'];

                  //user permission table
                  $sql = 'SELECT * FROM users_permissions WHERE student_id = "'.$student_id['student_id'].'"';
                  $result = $conn->query($sql);
                  $users_permission = $result->fetch_assoc();

                  $_SESSION['userPermission'] = $users_permission['account_type'];

                  $conn->close();

               }catch (mysqli_sql_exception $e){
                  $error = $e->getMessage();
                  echo $error;
               }

            }
            
         ?>
      </section>
   </main>

   <?php

   //Update side bar profile info
   echo "<div class = 'user_info'>
   <p>$_SESSION[userFirstName] $_SESSION[userLastName]</p><br>
   <img id='user_info_img' src='images/img_avatar$_SESSION[userAvatar].png' alt = 'avatar$_SESSION[userAvatar]'><br>
   <p>Email: $_SESSION[userEmail] </p><br>
   <p>Program: <br> $_SESSION[userProgram] </p>
   </div>"; 

   ?>

   <?php

   //Check if logged in, if not send to login.php
   if(!isset($_SESSION['userID'])){
      echo '<script>window.location = "login.php"</script>';
   }

   ?>

</body>

</html>