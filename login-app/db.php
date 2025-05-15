<?php

$conn = mysqli_connect("localhost","root","", "login_app");
if($conn){
//    echo "connected";
}else{
    echo "not connected" . mysqli_error($conn);
}