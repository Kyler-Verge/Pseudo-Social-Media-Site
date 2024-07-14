<?php
   //start session, to save user_id primarily
   session_start();

?>

<!DOCTYPE html>
<html lang="en">

<!--Kyler Verge 101114854-->

<head>
   <meta charset="utf-8">
   <title>SYSCX user list</title>
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
      <a href="user_list.php">User List</a>
   </nav>

   <main>
      <section>
         <h2>User List</h2>

         <?php

         if($_SESSION['userPermission'] == 0){
            
            //servers info
            include("connection.php");

            try{

                $conn = new mysqli($server_name, $username, $password, $database_name);

                echo "<table border='1'>";
                echo "<tr> <td> Student ID  </td> <td> First Name  </td> <td> Last Name  </td> <td> Student Email  </td> <td> Program  </td> <td> Account Type  </td>  </tr>";

                //user info table
                $sql = 'SELECT * FROM users_info';
                $result = $conn->query($sql);

                while($users_info = $result->fetch_assoc()){

                    $sql = 'SELECT program FROM users_program WHERE student_id = "'.$users_info['student_id'].'"';
                    $programResult = $conn->query($sql);
                    $users_program = $programResult->fetch_assoc();

                    $sql = 'SELECT account_type FROM users_permissions WHERE student_id = "'.$users_info['student_id'].'"';
                    $accountResult = $conn->query($sql);
                    $users_account = $accountResult->fetch_assoc();

                    echo "<tr> <td> ".$users_info['student_id']." </td> <td> ".$users_info['first_name']." </td> <td> ".$users_info['last_name']." </td> <td> ".$users_info['student_email']."  </td> <td> ".$users_program['program']."   </td> <td> ".$users_account['account_type']."  </td>  </tr>";

                }

                $conn->close();


            }catch (mysqli_sql_exception $e){
                $error = $e->getMessage();
                echo $error;
            }
        }

        else{
            echo 'Invalid Account Type';
        }

         ?>

        </section>
    </main>

</body>

</html>