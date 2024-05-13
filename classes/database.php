<?php
 
class database{
 
    function opencon(){
        return new PDO('mysql:host=localhost; dbname=loginmethod', 'root', '');
    }
    function check($username, $password){
        $con = $this->opencon();
        $query = "Select * from users WHERE user_name='".$username."'&&pass_word='".$password."'";
        return $con->query($query)->fetch();
    }
    function signup($username, $password,  $firstname, $lastname, $birthday, $sex){
        $con = $this->opencon();
 
        $query = $con->prepare("SELECT user_name FROM users WHERE user_name = ?");
        $query->execute([$username]);
        $existingUser= $query->fetch();
        if ($existingUser){
            return false;
        }
        $query = $con->prepare("INSERT INTO users (user_name, pass_word, firstName, lastName, birthday, sex) VALUES (?, ?, ?, ?, ?,?)");
        return $query->execute([$username, $password, $firstname, $lastname, $birthday,$sex]);
    }

    function signupUser($username, $password, $firstName, $lastName, $birthday, $sex) {
        $con = $this->opencon();
 
        $query = $con->prepare("SELECT user_name FROM users WHERE user_name = ?");
        $query->execute([$username]);
        $existingUser= $query->fetch();
        if ($existingUser){
            return false;
        }
        $query = $con->prepare("INSERT INTO users (firstName, lastName, birthday, sex, user_name, pass_word) VALUES (?, ?, ?, ?, ?,?)");
        $query->execute([$firstName, $lastName, $birthday, $sex, $username, $password]); 
        return $con->lastInsertId();
    }

    function insertAddress($user_id, $street, $barangay, $city, $province) {
        $con = $this->opencon();
        return $con->prepare("INSERT INTO user_address (user_id, user_add_street, user_add_barangay, user_add_city, user_add_province) VALUES (?, ?, ?, ?, ?)")
        ->execute([$user_id, $street, $barangay, $city, $province]);
 }
 
    function view()
    {
        $con = $this->opencon();
        return $con->query("SELECT users.user_id, users.user_name, users.pass_word, users.firstName, users.lastName, users.birthday, users.sex, CONCAT(user_address.user_add_street,' ', user_address.user_add_barangay,' ', user_address.user_add_city,' ', user_address.user_add_province) AS address FROM users JOIN user_address ON users.user_id=user_address.user_id")->fetchAll();
} 
function delete($id){
    try {
        $con = $this->opencon();
        $con->beginTransaction();

            $query = $con->prepare("DELETE FROM user_address WHERE user_id = ?");
            $query->execute([$id]);

            $query2 = $con->prepare("DELETE FROM users WHERE user_id = ?");
            $query2->execute([$id]);

            $con->commit();
            return true; 
    }   catch (PDOException $e) {
        $con->rollBack();
        return false;
        }
    }
}
