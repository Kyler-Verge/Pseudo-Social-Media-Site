<?php
   //start session, to save user_id primarily
   session_start();

?>

<!DOCTYPE html>
<html lang="en">

<!--Kyler Verge 101114854-->

<head>
   <meta charset="utf-8">
   <title>Update SYSCX profile</title>
   <link rel="stylesheet" href="assets/css/reset.css">
   <link rel="stylesheet" href="assets/css/style.css">
   
</head>

<body>
   <header>
      <h1>SYSCX</h1>
      <p>Social media for SYSC students in Carleton University</p>
   </header>
   <nav>
      <a class="active" href="index.php">Home</a>
      <a href="profile.php">Profile</a>
      <a href="logout.php">Log Out</a>
      <?php

         if($_SESSION['userPermission'] == 0){
            echo '<a href="user_list.php">User List</a>';
         }

      ?>
   </nav>

   <main>
      <section>
         <h2>Update Profile information</h2>
         <form name="profileForm" method="post" action="">
            <fieldset>

               <!--Personal Information Input-->
               <legend><span>Personal information</span></legend>

               <table>
                  <tr>
                     <td><label>First Name: </label><input type="text" name="first_name" id="first_name">
                         <label>Last Name: </label><input type="text" name="last_name" id="last_name">
                         <label>DOB:</label><input type="date" name="DOB" id="DOB"></td>
                  </tr>
               </table>

               <!--Address Input-->
               <div class="legend2">
                  <p>Address</p>
               </div>

               <table>
                  <tr>
                     <td> <label>Street Number:</label><input type="number" min="1" name="street_number">
                          <label>Street Name:</label><input type="text" name="street_name"> </td>
                  </tr>

                  <tr>
                     <td><label>City:</label><input type="text" name="city">
                         <label>Province:</label><input type="text" name="province">
                         <label>Postal Code:</label><input type="text" name="postal_code"></td>
                  </tr>
               </table>

               <!--Profile Information Input-->
               <div class="legend2">
                  <p>Profile Information</p>
               </div>

               <table>
                  <tr>
                     <td><label>Email address:</label><input type="text" name="student_email" id="student_email"></td>
                  </tr>

                  <tr>
                     <td><label>Program</label>
                        <select name="program" id="program">
                           <option>Choose Program</option>
                           <option>Computer Systems Engineering</option>
                           <option>Software Engineering</option>
                           <option>Communications Engineering</option>
                           <option>Biomedical and Electrical</option>
                           <option>Electrical Engineering</option>
                           <option>Special</option>
                        </select></td>
                  </tr>

                  <tr>
                     <td><label>Choose your Avatar</label>
                        <div class="avatar_img">
                           <input type="radio" name="avatar" value="1"><img src="images/img_avatar1.png" alt="avatar1">
                           <input type="radio" name="avatar" value="2"><img src="images/img_avatar2.png" alt="avatar2">
                           <input type="radio" name="avatar" value="3"><img src="images/img_avatar3.png" alt="avatar3">
                           <input type="radio" name="avatar" value="4"><img src="images/img_avatar4.png" alt="avatar4">
                           <input type="radio" name="avatar" value="5"><img src="images/img_avatar5.png" alt="avatar4"> 
                        </div></td>
                  </tr>

                  <!--Submit and Reset buttons-->
                  <tr>
                     <td><input type="submit" value="Submit" name="profile"><input type="reset"></td>
                  </tr>
               </table>
               
            </fieldset>
         </form>

         <!--PHP Script-->
         <?php

         

            //register.php submit button was pressed
            if(isset($_POST["register"])){

                  //servers info
                  include("connection.php");

                  try{
                     $conn = new mysqli($server_name, $username, $password, $database_name);

                     //Check if inputted email is already in database
                     $sql = 'SELECT * FROM users_info WHERE student_email = "'.$_POST["student_email"].'"';
                     $checkResult = $conn->query($sql);

                     if($checkResult){
                        //If the inputted email is already in database, send back to register.php, send error message and 
                        //exit the submit form.
                        if(mysqli_num_rows($checkResult) > 0){
                           $_SESSION["dupeEmail"] = "This email is already used";
                           echo '<script>window.location = "register.php"</script>';
                           exit();
                        } 
                     }

                     //The inputted email is new, proceed with creating new rows to tables
                  
                     //Add a row to the users_info table
                     $sql = 'INSERT INTO users_info(student_email, first_name, last_name, dob) VALUES (?,?,?,?);';
                     $statement = $conn->prepare($sql);
                     $statement->bind_param('ssss', $_POST["student_email"], $_POST["first_name"], $_POST["last_name"], $_POST["DOB"]);
                     $statement->execute();

                     //Retrieve Created Row from users_info table and fetch the student id value to use to insert into other tables
                     $sql = 'SELECT student_id FROM users_info WHERE student_email = "'.$_POST["student_email"].'"';
                     $result = $conn->query($sql);
                     $student_id = $result->fetch_assoc();

                     //Insert fetched student id into users_program table
                     $sql = 'INSERT INTO users_program(student_id, program) VALUES (?,?);';
                     $statement = $conn->prepare($sql);
                     $statement->bind_param('is', $student_id['student_id'], $_POST["program"]);
                     $statement->execute();

                     //insert fetched student id into users_avatar table
                     $sql = 'INSERT INTO users_avatar(student_id, avatar) VALUES (?, 0);';
                     $statement = $conn->prepare($sql);
                     $statement->bind_param('i', $student_id['student_id']);
                     $statement->execute();

                     //$sql = 'INSERT INTO users_address(student_id, street_number, street_name, city, province, postal_code) VALUES ("'.$student_id['student_id'] .'", 0, NULL, NULL, NULL, NULL);';
                     //$conn->query($sql);
                     $sql = 'INSERT INTO users_address(student_id, street_number, street_name, city, province, postal_code) VALUES (?, 0, NULL, NULL, NULL, NULL);';
                     $statement = $conn->prepare($sql);
                     $statement->bind_param('i', $student_id['student_id']);
                     $statement->execute();

                     //Insert fetched student id into users_passwords table
                     $sql = 'INSERT INTO users_passwords(student_id, password) VALUES (?,?);';
                     $statement = $conn->prepare($sql);
                     //Hash password
                     $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
                     $statement->bind_param('is', $student_id['student_id'], $password);
                     $statement->execute();
                     
                     //Insert fetched student id into users_permissions
                     $sql = 'INSERT INTO users_permissions(student_id, account_type) VALUES (?,1);';
                     $statement = $conn->prepare($sql);
                     $statement->bind_param('i', $student_id['student_id']);
                     $statement->execute();

                     //Close connection to Database
                     $conn->close();

                     //Session variables to be used for profile changes and user_posts
                     $_SESSION["userID"] = $student_id['student_id'];
                     $_SESSION["userFirstName"] = $_POST['first_name'];
                     $_SESSION["userLastName"] = $_POST['last_name'];
                     $_SESSION["userEmail"] = $_POST['student_email'];
                     $_SESSION["userDOB"] = $_POST['DOB'];
                     $_SESSION["userProgram"] = $_POST['program'];
                     $_SESSION["userAvatar"] = 0;
                     $_SESSION['userStreetNumber'] = "";
                     $_SESSION['userStreetName'] = "";
                     $_SESSION['userCity'] = "";
                     $_SESSION['userProvince'] = "";
                     $_SESSION['userPostalCode'] = "";
                     $_SESSION['userPermission'] = 1;


                     //Insert values into profile.phps input fields
                     //echo "SESSION: Id: $_SESSION[userID]";
                     echo "<script> document.getElementById('first_name').value = '$_SESSION[userFirstName]'; </script> ";
                     echo "<script> document.getElementById('last_name').value = '$_SESSION[userLastName]'; </script> ";
                     echo "<script> document.getElementById('student_email').value = '$_SESSION[userEmail]'; </script> ";
                     echo "<script> document.getElementById('DOB').value = '$_SESSION[userDOB]'; </script> ";
                     echo "<script> document.getElementById('program').value = '$_SESSION[userProgram]'; </script> ";

                     //Go to profile php, action="profile.php"

                  }catch (mysqli_sql_exception $e){
                     $error = $e->getMessage();
                     echo $error;
                  }
              }
            

            //profile.php submit button was pressed
            elseif(isset($_POST["profile"])){

               //servers info
               include("connection.php");

               try{
                  $conn = new mysqli($server_name, $username, $password, $database_name);

                  //Update users_info table
                  //$sql = 'UPDATE users_info SET student_email = "'.$_POST["student_email"].'", first_name = "'.$_POST["first_name"].'", last_name = "'.$_POST["last_name"].'", dob = "'.$_POST["DOB"].'" WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  $sql = 'UPDATE users_info SET student_email = ?, first_name = ?, last_name = ?, dob = ? WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  $statement = $conn->prepare($sql);
                  $statement->bind_param('ssss', $_POST["student_email"], $_POST["first_name"], $_POST["last_name"], $_POST["DOB"]);
                  $statement->execute();
                  //$conn->query($sql);

                  //Update users_program table
                  //$sql = 'UPDATE users_program SET program = "'.$_POST["program"].'" WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  //$conn->query($sql);
                  $sql = 'UPDATE users_program SET program = ? WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  $statement = $conn->prepare($sql);
                  $statement->bind_param('s', $_POST["program"]);
                  $statement->execute();

                  //Update users_avatar table
                  //$sql = 'UPDATE users_avatar SET avatar = "'.$_POST["avatar"].'" WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  //$conn->query($sql);
                  $sql = 'UPDATE users_avatar SET avatar = ? WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  $statement = $conn->prepare($sql);
                  $statement->bind_param('i', $_POST["avatar"]);
                  $statement->execute();


                  //Update users_address table
                  //$sql = 'UPDATE users_address SET street_number = "'.$_POST["street_number"].'", street_name = "'.$_POST["street_name"].'", city = "'.$_POST["city"].'", province = "'.$_POST["province"].'", postal_code = "'.$_POST["postal_code"].'" WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  //$conn->query($sql);
                  $sql = 'UPDATE users_address SET street_number = ?, street_name = ?, city = ?, province = ?, postal_code = ? WHERE student_id = "'.$_SESSION["userID"].'" ;';
                  $statement = $conn->prepare($sql);
                  $statement->bind_param('issss', $_POST["street_number"], $_POST["street_name"], $_POST["city"], $_POST["province"], $_POST["postal_code"]);
                  $statement->execute();

                  //Close connection to Database
                  $conn->close();

                  //Update Session variables
                  $_SESSION["userFirstName"] = $_POST['first_name'];
                  $_SESSION["userLastName"] = $_POST['last_name'];
                  $_SESSION["userEmail"] = $_POST['student_email'];
                  $_SESSION["userDOB"] = $_POST['DOB'];
                  $_SESSION["userProgram"] = $_POST['program'];
                  $_SESSION["userAvatar"] = $_POST['avatar'];
                  $_SESSION['userStreetNumber'] = $_POST['street_number'];
                  $_SESSION['userStreetName'] = $_POST['street_name'];
                  $_SESSION['userCity'] = $_POST['city'];
                  $_SESSION['userProvince'] = $_POST['province'];
                  $_SESSION['userPostalCode'] = $_POST['postal_code'];


                  //Insert/Update values into profile.phps input fields on refresh
                  echo "<script> document.getElementById('first_name').value = '$_SESSION[userFirstName]'; </script> ";
                  echo "<script> document.getElementById('last_name').value = '$_SESSION[userLastName]'; </script> ";
                  echo "<script> document.getElementById('student_email').value = '$_SESSION[userEmail]'; </script> ";
                  echo "<script> document.getElementById('DOB').value = '$_SESSION[userDOB]'; </script> ";
                  echo "<script> document.getElementById('program').value = '$_SESSION[userProgram]'; </script> ";
                  echo "<script> document.profileForm.avatar.value = '$_SESSION[userAvatar]'; </script> ";
                  echo "<script> document.profileForm.street_number.value = '$_SESSION[userStreetNumber]'; </script> ";
                  echo "<script> document.profileForm.street_name.value = '$_SESSION[userStreetName]'; </script> ";
                  echo "<script> document.profileForm.city.value = '$_SESSION[userCity]'; </script> ";
                  echo "<script> document.profileForm.province.value = '$_SESSION[userProvince]'; </script> ";
                  echo "<script> document.profileForm.postal_code.value = '$_SESSION[userPostalCode]'; </script> ";

               }catch (mysqli_sql_exception $e){
                  $error = $e->getMessage();
                  echo $error;
               }

            }

            //Update profile.php input fields with session variables when going into profile.php from index.php
            if(isset($_SESSION['userID'])){
               echo "<script> document.getElementById('first_name').value = '$_SESSION[userFirstName]'; </script> ";
               echo "<script> document.getElementById('last_name').value = '$_SESSION[userLastName]'; </script> ";
               echo "<script> document.getElementById('student_email').value = '$_SESSION[userEmail]'; </script> ";
               echo "<script> document.getElementById('DOB').value = '$_SESSION[userDOB]'; </script> ";
               echo "<script> document.getElementById('program').value = '$_SESSION[userProgram]'; </script> ";
               echo "<script> document.profileForm.avatar.value = '$_SESSION[userAvatar]'; </script> ";
               echo "<script> document.profileForm.street_number.value = '$_SESSION[userStreetNumber]'; </script> ";
               echo "<script> document.profileForm.street_name.value = '$_SESSION[userStreetName]'; </script> ";
               echo "<script> document.profileForm.city.value = '$_SESSION[userCity]'; </script> ";
               echo "<script> document.profileForm.province.value = '$_SESSION[userProvince]'; </script> ";
               echo "<script> document.profileForm.postal_code.value = '$_SESSION[userPostalCode]'; </script> ";
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