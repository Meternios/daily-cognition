<?php
    $connect = new PDO("mysql:host=localhost;dbname=meternio_dailyCognition", "meternio_dailyCo", "TVY8!W9:PY#baft.");
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();
    error_log("Die Oracle-Datenbank ist nicht erreichbar!", 0);
    if($received_data->action == 'insert')
    {
        //Add Cognition
        $data = array(
        ':title' => $received_data->title,
        ':description' => $received_data->description
        );

        $cogQuery = "
        INSERT INTO cognitions 
        (Title, Description) 
        VALUES (:title, :description);
        ";

        $statement = $connect->prepare($cogQuery);
        
        $statement->execute($data);

        $cogid = $connect->lastInsertId();

        //Only add Tags if not already in DB
        $tagsToAdd = array_diff($received_data->tags, $received_data->items);

        if(!empty($tagsToAdd)){
            $tagsQuery = "
            INSERT INTO cognition_tags (Name)
            VALUES ";
            
            foreach ($tagsToAdd as $key => $tag) {
                $tagsQuery .= "('".$tag."')";
                if ($key !== array_key_last($tagsToAdd)){
                    $tagsQuery .= ",";
                }
            }

            $statement = $connect->prepare($tagsQuery);
        
            $statement->execute();
        }

        //Connect Tags and Cognitions
        $tagIdsQuery = "
        SELECT ID
        FROM cognition_tags
        WHERE Name in ";
    
        $tagIdsQuery .= "('".implode("','",$received_data->tags)."')";
        
        $statement = $connect->prepare($tagIdsQuery);
    
        $statement->execute();

        $tagIds = $statement->fetchAll(PDO::FETCH_ASSOC);

        //Insert into cog_tags
        $cog_tagsQuery = "
        INSERT INTO cogs_tags (FK_cognition_tag, FK_cognition)
        VALUES ";
        
        foreach ($tagIds as $key => $tagId) {
            $cog_tagsQuery .= "(".$tagId['ID'].",".$cogid.")";
            if ($key !== array_key_last($tagIds)){
                $cog_tagsQuery .= ",";
            }
        }

        $statement = $connect->prepare($cog_tagsQuery);
    
        $statement->execute();

        $output = array(
            'message' => 'Cognition inserted'
        );
        
        echo json_encode($output);
    }

    if($received_data->action == 'getTags')
    {
        $output = [];

        $query = "
        SELECT Name
        FROM cognition_tags
        ";
        
        $statement = $connect->prepare($query);
        
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $output[] = $row['Name'];
        }

        echo json_encode($output);
    }
?>