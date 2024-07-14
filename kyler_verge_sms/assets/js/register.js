//Kyler Verge
//Password check in register.php JS
document.addEventListener('DOMContentLoaded', function(){

    document.querySelector("#registerForm").addEventListener("submit", function(e){

        //retrieve the (confirm) password values inputted in the form
        const pw = document.getElementById("pw");
        const pwValue = pw.value;

        const cpw = document.getElementById("cpw");
        const cpwValue = cpw.value;

        console.log(pwValue);
        console.log(cpwValue);

        //If password field is empty
        if(pwValue === ""){
            console.log("empty password");
            window.alert("Empty password");

            e.preventDefault();
        }

        //If password and confirm password dont match
        else if(pwValue != cpwValue){
            console.log("passwords dont match");
            window.alert("Password and Confirm Password do not match");
            e.preventDefault();
        }

    })
    
});