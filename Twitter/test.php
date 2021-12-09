<?php
 $username = "root";
 $password = "";
 $conn = new PDO("mysql:host=localhost;dbname=twitter",$username,$password);
 $stmt = $conn->prepare("INSERT INTO users (firstName , lastName , userName , email , password , profileImage , profileCover) VALUES (:fname , :lname , :username ,:email , :pass , :image , :cover )");
 $fn = "ahmed";
 $stmt->bindParam(":fn",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":ln",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":un",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":em",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":pass",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":image",$fn,PDO::PARAM_STR);
 $stmt->bindParam(":cover",$fn,PDO::PARAM_STR);
 $stmt->execute();