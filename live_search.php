<?php
require_once('classes/database.php');


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST['search'])) {
        $searchterm = $_POST['search']; 
        $con = new database();

        try {
            $connection = $con->opencon();
            
            // Check if the connection is successful
            if ($connection) {
                // SQL query with JOIN
                $query = $connection->prepare("SELECT users.user_id, users.firstName, users.lastName, users.birthday, users.sex, users.user_name, users.user_profile_picture, CONCAT(	user_add_city,', ', user_add_province) AS address FROM users INNER JOIN user_address ON users.user_id = user_address.user_id WHERE users.user_name LIKE ? OR users.user_id LIKE ? OR CONCAT(user_add_city,', ', user_add_province) LIKE ? OR users.firstName LIKE ?");
                $query->execute(["%$searchterm%","%$searchterm%","%$searchterm%", "%$searchterm%"]);
                $users = $query->fetchAll(PDO::FETCH_ASSOC);

                // Generate HTML for table rows
                $html = '';
                foreach ($users as $user) {
                    $html .= '<tr>';
                    $html .= '<td>' . $user['user_id'] . '</td>';
                    $html .= '<td><img src="' . htmlspecialchars($user['user_profile_picture']) . '" alt="Profile Picture" style="width: 50px; height: 50px; border-radius: 50%;"></td>';
                    $html .= '<td>' . $user['firstName'] . '</td>';
                    $html .= '<td>' . $user['lastName'] . '</td>';
                    $html .= '<td>' . $user['birthday'] . '</td>';
                    $html .= '<td>' . $user['sex'] . '</td>';
                    $html .= '<td>' . $user['user_name'] . '</td>';
                    $html .= '<td>' . $user['address'] . '</td>';
                    $html .= '<td>'; // Action column
                    $html .= '<div class="btn-group" role="group">';
                    $html .= '<form action="update.php" method="post" style="display: inline;">';
                    $html .= '<input type="hidden" name="id" value=".'. $user['user_id'].'">';
                    $html .= '<button type="submit" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></button>';
                    $html .= '</button>';
                    $html .= '</form>';
                    $html .= '<form method="POST" class="d-inline">';
                    $html .= '<input type="hidden" name="id" value=".'. $user['user_id'].'">';
                    $html .= '<button type="submit" name="delete" class="btn btn-danger btn-sm" onclick="return confirm("Are you sure you want to delete this user?")">';
                    $html .= '<i class="fas fa-trash-alt"></i>';
                    $html .= '</button>';
                    $html .= '</form>';
                    $html .= '</td>';
                    $html .= '</tr>';
                }
                echo $html;
            } else {
                echo json_encode(['error' => 'Database connection failed.']);
            }
        } catch (PDOException $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['error' => 'No search query provided.']);
    }
} 