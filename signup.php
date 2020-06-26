<?php

session_start();

$conn=new mysqli('localhost','root','','user-verification')

// if($conn->connect_error){
//     die('Database error:' .$conn->connect_error);
// }
// else{


// $errors = array();
// $username="";
// $email="";


if(isset($_POST['signup-btn'])){
    $username=$_POST['username'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $confpassword=$_POST['confpassword'];

    if(empty($username)){
        $errors['username']="username required";
    }
    if(!filter_var($email,FILTER_VALIDATE_EMAIL)){
        $errors['email']="email address is invalid";
    }
    if(empty($email)){
        $errors['email']="email required"
    }
    if ($password !==$confpassword){
        $errors['password']="the password doesnot match";
    }
    $emailquery="*SELECT *FROM users WHERE email=? LIMIT 1"
    $stmt = $conn->prepare($emailquery);
    $stmt->bind_param('s',$email);
    $stmt->execute();
    $result = $stmt->get_result();
    $userCount=$result->num_rows;
    $stmt->close();

    if($userCount>0){
        $errors['email']="Email is already exist";
    }
    if (count($errors)==0){
        $password=password_hash($password,PASSWORD_DEFAULT);
        $token=bin2hex(random_bytes(50));
        $verified=false;

        $sql="INSERT INTO users (username,email,verified,token,password) VALUES(?,?,?,?,?)";
        $stmt=$conn->prepare($sql);
        $stmt->bind_param('ssbss',$username,$email,$verified,$token,$password);

        if($stmt->execute()){
            $user_id=$conn->insert_id;
            $_SESSION['id']=$user_id;
            $_SESSION['username']=$user_username;
            $_SESSION['email']=$email;
            $_SESSION['verified']=$verified;

            $_SESSION['message']="You are now logged in!"
            $_SESSION['alert-class']="alert-success";
            header('location:home.html');
            exit();

        }
        else{
            $errors['db_error']="Database error: failed to register";
        }
    }





}



// }




?>