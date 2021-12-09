<?php
    class Account {
        private $pdo ;
        private $errorArray = array();
        public function __construct()
        {
            $this->pdo=Database::instance();
        }

        public function register($fn,$ln,$un,$em,$pass,$pass2){
            $this->validateFirstName($fn);
            $this->validateLastName($ln);
            $this->validateEmail($em);
            $this->validatePasswords($pass,$pass2);
            if(empty($this->errorArray)){
                return $this->insertUserDetails($fn,$ln,$un,$em,$pass);
            }else{
                return false;
            }
        }

        //! Validate first Name
        private function validateFirstName($fn){
            if($this->length($fn,2,25)){
                array_push($this->errorArray,Constants::$firstNameCharacter);
                return ;
            }
        }

        //! Validate last Name
        private function validateLastName($ln){
            if($this->length($ln,2,25)){
                array_push($this->errorArray,Constants::$lastNameCharacter);
                return ;
            }
        }

        //! Generate User Name
        public function generateUserName($fn,$ln){
            if(!empty($fn) && !empty($ln)){
                if(!in_array(Constants::$firstNameCharacter,$this->errorArray) && !in_array(Constants::$lastNameCharacter,$this->errorArray)){
                    $username = strtolower($fn."".$ln);
                    if($this->checkUsername($username)){
                        $screenRand = rand();
                        $userLink = "".$screenRand."".$username;
                    }else{
                        $userLink=$username;
                    }
                    return $userLink;
                }
            }
        }

        private function checkUsername($username){
            $stmt=$this->pdo->prepare("SELECT username FROM users WHERE username=:username");
            $stmt->bindParam(":username",$username,PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->rowCount();
            if($count != 0) {
                return true;
            }else {
                return false;
            }
        }

        //! validate Email
        private function validateEmail($em){
            $stmt = $this->pdo->prepare("SELECT email FROM users WHERE email=:email");
            $stmt->bindParam(":email",$em,PDO::PARAM_STR);
            $stmt->execute();
            $count = $stmt->rowCount();
            if($count != 0) {
                array_push($this->errorArray,Constants::$emailtaken);
                return ;
            }
            if(!filter_var($em, FILTER_VALIDATE_EMAIL)){
                array_push($this->errorArray,Constants::$emailInValid);
            }
        }

        //! Validate Passwords
        private function validatePasswords($pass,$pass2){
            if($pass != $pass2){
                array_push($this->errorArray,Constants::$passwordDoNotMatch);
                return ;
            }
            if($this->length($pass,7,25)){
                array_push($this->errorArray,Constants::$passwordTooShort);
                return;
            }
            
            if($this->length($pass2,7,25)){
                array_push($this->errorArray,Constants::$passwordTooShort);
                return;
            }

            if(!preg_match("/[A-Za-z0-9@#$%&* ]/",$pass)){
                array_push($this->errorArray,Constants::$passwordNotAlphNumber);
                return ;
            }
        }
        private function insertUserDetails($fn,$ln,$un,$em,$pw){
        $hash_pass = password_hash($pw,PASSWORD_BCRYPT);
        $rand = rand(0,2);
        if($rand === 0) {
        $profilePic = "Front-End//assets//images/defaultProfilePic.png";
        $profileCover  = "Front-End//assets//images//backgroundCoverPic.svg";
        }elseif($rand === 1) {
        $profilePic = "Front-End//assets//images/defaultPic.svg";
        $profileCover  = "Front-End//assets//images//backgroundImage.svg";
        }elseif($rand === 2) {
        $profilePic = "Front-End//assets//images//avatar.png";
        $profileCover  = "Front-End//assets//images//backgroundCoverPic.svg";
        }

        $stmt = $this->pdo->prepare("INSERT INTO users (firstName , lastName , userName , email , password , profileImage , profileCover) VALUES (:fname , :lname , :username ,:email , :pass , :image , :cover )");
        $stmt->bindParam(":fname",$fn,PDO::PARAM_STR);
        $stmt->bindParam(":lname",$ln,PDO::PARAM_STR);
        $stmt->bindParam(":username",$un,PDO::PARAM_STR);
        $stmt->bindParam(":email",$em,PDO::PARAM_STR);
        $stmt->bindParam(":pass",$hash_pass,PDO::PARAM_STR);
        $stmt->bindParam(":image",$profilePic,PDO::PARAM_STR);
        $stmt->bindParam(":cover",$profileCover,PDO::PARAM_STR);
        // var_dump($stmt);
        $stmt->execute();
        return $this->pdo->lastInsertId();
        }

        private function length($input,$min,$max){
            if(strlen($input) < $min){
                return true;
            }else if(strlen($input) > $max){
                return true;
            }
        }

        //Get Error
        public function getError($error){
            if(in_array($error,$this->errorArray)){
                return "<span class='errorMessage'>$error</span>";
            }
        }

    }
?>