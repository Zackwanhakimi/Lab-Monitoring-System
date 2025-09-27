<?php 

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <title>Lab Monitoring System</title>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <style>
        /* Reset default margins */
        * {
            font: "Poppins";
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Body setup */
        body {
            font-family: Arial, sans-serif;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: url('https://i.ibb.co/BCkxxd8/ai-gen.png');
            backdrop-filter: blur(2px);
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            z-index: 0;
        }

        /* Overlay effect applied directly to body */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.25); /* Semi-transparent black */
            z-index: 1; /* Ensure it sits behind the header and content */
        }

        /* Header style */
        header {
            display: flex;
            background-color: #f4f4f4;
            align-items: center;
            color: black;
            text-align: center;
            padding: 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 10;
        }
        

        /* make the button to be far left */
        .split {
            margin-left: auto;
        }

        /* design the log in button */
        .login-btn {
            margin: 0; /* Reset margins */
            padding: 10px 15px; /* Add padding for better appearance */
            background-color: #000f73;
            color: white;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            align-self: flex-end;
            transition: 0.1s;
            
        }

        .login-btn:hover {
            background-color:#2c356c; /* Add a hover effect */
            transform: translateY(-5px);
        }

        .login-btn:active {
            background-color: #2c356c; /* Add a hover effect */
            transform: translateY(0px);
        }

        .contentHeader {
            height: 50px; /*77px as the header original hieght and 50px as the added value under the header*/
            z-index: 9;
        }

        /* Website title */
        h1.title {
            position: fixed;
            font-family: "Poppins", Arial, sans-serif;
            width: 100%;
            color: #f4f4f4;
            background-color:#000f73;
            padding: 10px;
            border: solid 10px #000f73;
            text-align: center;
            margin-bottom: 20px;
            
            
        }

        .content{
            position: fixed;
            margin-top: 77px;
            height: 50px;
            z-index: 100;
        }
        .centeredContent {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            height: calc(100vh - 77px); /* Full height minus the header */
            margin-top: 77px; /* Ensure it starts below the fixed header */
            
        }

        .belowContent {
            display: flex;
            align-items: center; /* Center-align the child elements */
            justify-content: center;
            gap: 50px; /* Add spacing between items (optional) */
            width: 100%;
            z-index: 100;
        }

        .belowContent a {
            width: 700px;
            height: 400px;
            background-position: center;
            background-size: cover;
            border: 2px solid black;
            padding: 10px;
            text-align: center;
            border-radius: 15px;
            transition: all 0.3s ease;
            
        }

        /* change the backdrop */
        .button1 {
            background-image: url('https://i.ibb.co/F899tsF/1.jpg'); 
        }

        .button2 {
            background-image: url('https://i.ibb.co/T0GgtHT/2.jpg');  
        }

        .belowContent a:hover{
            transform: scale(1.05);
        }

        .belowContent a:active{
            transform: scale(0.95);
        }

        img{
            max-height: 45px;
            margin-right: 20px;

        }


    </style>
</head>
<body>

    <header>
        <img src="https://kppim.uitm.edu.my/images/logo/LOGO_KPPIM-02.png" alt="logo">
        <h1>Lab Monitoring System</h1>
        <a class="split"  href="login.php?userType=Admin">
            <button class="login-btn">
                <span class="btn-text">Log in as Admin</span> <!--login-->
                <span class="btn-icon"><i class="fa fa-lock" aria-hidden="true"></i></span>
            </button>
        </a>
    </header>
    
    
    <div class="content">
        
        <div class="contentHeader">
            <h1 class="title">CHOOSE YOUR LOGIN OPTION</h1>
        </div>
    </div>

    <div class="centeredContent">
    <div class="belowContent">
        <a class="button1" href="login.php?userType=Student"></a>
        <a class="button2" href="login.php?userType=Lecturer"></a>
    </div>
</div>

</body>
</html>
