<?php
   //start session, to save user_id primarily
   session_start();

?>

<!--Kyler Verge-->

<!DOCTYPE html>
<html lang="en">

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
      <a href="register.php">Register</a>
      <a href="login.php">Log in</a>
   </nav>

    <!--Login Page Main body-->
    <main>
         <section>
            <h2>Login</h2>
            <form method= "post" action="index.php">
               <fieldset>

                  <table>
                    <!--Student Email for login-->
                     <tr>
                        <td><label>Email address: </label><input type="text" name="student_email"></td>
                     </tr>

                     <!--Student password for login-->
                     <tr>
                        <td><label>Password: </label><input type="text" name="password"></td>
                     </tr>

                     <!--login and Reset buttons-->
                     <tr>
                        <td><input type="submit" value="Submit" name="login"><input type="reset"></td>
                     </tr>
                  </table>

               </fieldset>
            </form>
            <?php
            //Print error message for submitting a duplicate email
            if(isset($_SESSION["noEmail"])){
               echo "$_SESSION[noEmail] <br>";
               echo "<a href=register.php>Don't have an account? Click here to create one!</a>";
               unset($_SESSION['noEmail']);
            }

            if(isset($_SESSION['wrongPassword'])){
               echo "$_SESSION[wrongPassword]";
               unset($_SESSION['wrongPassword']);
            }

            ?>
         </section>
      </main>

</body>