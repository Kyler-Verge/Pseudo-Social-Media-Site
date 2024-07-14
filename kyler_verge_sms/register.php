<?php
   //start session, to save user_id primarily
   session_start();

?>

<!DOCTYPE html>
<html lang="en">

   <!--Kyler Verge 101114854-->

   <head>
      <meta charset="utf-8">
      <title>Register on SYSCX</title>
      <link rel="stylesheet" href="assets/css/reset.css">
      <link rel="stylesheet" href="assets/css/style.css">
      <script src="assets/js/register.js"></script>
   </head>

   <body>
      <header>
         <h1>SYSCX</h1>
         <p>Social media for SYSC students in Carleton University</p>
      </header>

      <!-- Left Side navbar-->
      <nav>
         <a href="register.php">Register</a>
         <a href="login.php">Log in</a>
      </nav>

      <!--Register Page Main body-->
      <main>
         <section>
            <h2>Register a new profile</h2>
            <form id= "registerForm" method= "post" action="profile.php">
               <fieldset>

                  <!--Personal Information Input-->
                  <legend><span>Personal information</span></legend>

                  <table>
                     <tr>
                        <td><label>First Name: </label><input type="text" name="first_name"></td>
                        <td><label>Last Name: </label><input type="text" name="last_name"></td>
                        <td><label>DOB:</label><input type="date" name="DOB"></td>
                     </tr>
                  </table>
               
                  <!--Profile Information Input-->
                  <div class="legend2">
                     <p>Profile Information</p>
                  </div>

                  <table>
                     <tr>
                        <td><label>Email address: </label><input type="text" name="student_email"></td>
                        <td><label>Password: </label><input type="text" name="password" id="pw"></td>
                        <td><label>Confirm Password: </label><input type="text" name="confirm_password" id="cpw"></td>
                     </tr>

                     <tr>
                        <td><label>Program</label>
                           <select name="program">
                              <option>Choose Program</option>
                              <option>Computer Systems Engineering</option>
                              <option>Software Engineering</option>
                              <option>Communications Engineering</option>
                              <option> Biomedical and Electrical</option>
                              <option>Electrical Engineering</option>
                              <option>Special</option>
                           </select>
                        </td>
                     </tr>


                     <!--Submit and Reset buttons-->
                     <tr>
                        <td><input type="submit" value="Submit" name="register">
                        <input type="reset"></td>
                     </tr>
                  </table>

               </fieldset>
            </form>
            <?php
            //Print error message for submitting a duplicate email
            if(isset($_SESSION["dupeEmail"])){
               echo "$_SESSION[dupeEmail]";
               echo "<br> <a href=login.php>Already have an account? Click here to log in</a>";
            }

            ?>
         </section>
      </main>

      <div class="user_info"></div>
      
   </body>
</html>