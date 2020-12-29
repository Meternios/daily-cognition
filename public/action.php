<?php
    $connect = new PDO("mysql:host=localhost;dbname=meternio_dailyCognition", "meternio_dailyCo", "TVY8!W9:PY#baft.");
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();
    if($received_data->action == 'insert')
    {
        $data = array(
        ':title' => $received_data->title,
        ':description' => $received_data->description
        );

        $query = "
        INSERT INTO cognitions 
        (Title, Description) 
        VALUES (:title, :description)
        ";
        
        $statement = $connect->prepare($query);
        
        $statement->execute($data);

        $output = array(
            'message' => 'Data Inserted'
        );
        
        echo json_encode($output);
    }
?>