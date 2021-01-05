<?php
    $connect = new PDO("mysql:host=localhost;dbname=meternio_dailyCognition", "meternio_dailyCo", "TVY8!W9:PY#baft.");
    $received_data = json_decode(file_get_contents("php://input"));
    $data = array();
    if(session_id() == '') {
        session_start();
    }
    if($received_data->action == 'googleSignin'){
        $_SESSION["user_id"] = $received_data->user_id;
        $output = fetchData($connect, $received_data, $data);

        $output['message'] = 'Logged in as '.$_SESSION["user_id"]." fetch data ".json_encode($output);
        
        echo json_encode($output);
    }

    if($received_data->action == 'getLoggedInUser'){
        if(isset($_SESSION["user_id"])){
            $output = fetchData($connect, $received_data, $data);
            $output['loggedIn'] = true;
            $output['message'] = 'Loggedin';
        }else{
            $output['loggedIn'] = false;
            $output['message'] = 'Loggedout';
        }
        
        echo json_encode($output);
    }

    if($received_data->action == 'delete'){
        //Delete Cognition
        $query = "
        DELETE FROM cognitions WHERE ID='".$received_data->cogID."'";

        $statement = $connect->prepare($query);
        
        $statement->execute();

        //Delete from Cogtags
        $query = "
        DELETE FROM cogs_tags WHERE FK_cognition='".$received_data->cogID."'";

        $statement = $connect->prepare($query);
        
        $statement->execute();
        
        $output['message'] = 'Cognition deleted with id'.$received_data->cogID;
        
        echo json_encode($output);
    }

    if($received_data->action == 'fetchData'){
        $output = fetchData($connect, $received_data, $data);

        $output['message'] = 'Logged in as '.$_SESSION["user_id"]." fetch data ".json_encode($output);
        
        echo json_encode($output);
    }

    if($received_data->action == 'insert')
    {
        //Add Cognition
        $data = array(
        ':title' => $received_data->title,
        ':description' => $received_data->description,
        ':user_id' => $_SESSION["user_id"],
        );

        $cogQuery = "
        INSERT INTO cognitions 
        (Title, Description, FK_user) 
        VALUES (:title, :description, :user_id);
        ";

        $statement = $connect->prepare($cogQuery);
        
        $statement->execute($data);

        $cogid = $connect->lastInsertId();

        //Only add Tags if not already in DB
        $tagsToAdd = array_diff($received_data->tags, $received_data->items);

        if(!empty($tagsToAdd)){
            $tagsQuery = "
            INSERT INTO cognition_tags (Name, FK_user)
            VALUES ";
            
            foreach ($tagsToAdd as $key => $tag) {
                $tagsQuery .= "('".$tag."', '".$_SESSION["user_id"]."')";
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
        WHERE FK_user = ".$_SESSION["user_id"]."
        AND Name in ";
    
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

        $output['message'] = 'Cognition inserted into '.$_SESSION["user_id"];
        
        echo json_encode($output);
    }

    if($received_data->action == 'update')
    {
        //update Cognition
        $data = array(
        ':title' => $received_data->title,
        ':description' => $received_data->description,
        ':cogId' => $received_data->cogID,
        );

        $cogQuery = "
        UPDATE cognitions
        SET Title = :title,
        Description = :description
        WHERE ID = :cogId
        ";

        $statement = $connect->prepare($cogQuery);
        
        $statement->execute($data);

        //Only add Tags if not already in DB
        $tagsToAdd = array_diff($received_data->tags, $received_data->items);

        if(!empty($tagsToAdd)){
            $tagsQuery = "
            INSERT INTO cognition_tags (Name, FK_user)
            VALUES ";
            
            foreach ($tagsToAdd as $key => $tag) {
                $tagsQuery .= "('".$tag."', '".$_SESSION["user_id"]."')";
                if ($key !== array_key_last($tagsToAdd)){
                    $tagsQuery .= ",";
                }
            }

            $statement = $connect->prepare($tagsQuery);
        
            $statement->execute();
        }

        //Delete already existing tags_cogs
        $query = "
        DELETE FROM cogs_tags WHERE FK_cognition='".$received_data->cogID."'";

        $statement = $connect->prepare($query);
        
        $statement->execute();

        //Connect Tags and Cognitions
        $tagIdsQuery = "
        SELECT ID
        FROM cognition_tags
        WHERE FK_user = ".$_SESSION["user_id"]."
        AND Name in ";
    
        $tagIdsQuery .= "('".implode("','",$received_data->tags)."')";
        
        $statement = $connect->prepare($tagIdsQuery);
    
        $statement->execute();

        $tagIds = $statement->fetchAll(PDO::FETCH_ASSOC);

        //Insert into cog_tags
        $cog_tagsQuery = "
        INSERT INTO cogs_tags (FK_cognition_tag, FK_cognition)
        VALUES ";
        
        foreach ($tagIds as $key => $tagId) {
            $cog_tagsQuery .= "(".$tagId['ID'].",".$received_data->cogID.")";
            if ($key !== array_key_last($tagIds)){
                $cog_tagsQuery .= ",";
            }
        }

        $statement = $connect->prepare($cog_tagsQuery);
    
        $statement->execute();

        $output['message'] = 'Cognition updated into '.$_SESSION["user_id"];
        
        echo json_encode($output);
    }

    if($received_data->action == 'getTags')
    {
        $query = "
        SELECT Name
        FROM cognition_tags
        WHERE FK_user in ('".$_SESSION["user_id"]."')
        ";

        $output = [
            'tags' => [],
            'message' => '',
        ];
        
        $statement = $connect->prepare($query);
        
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $output['tags'][] = $row['Name'];
        }

        $output['message'] = 'Get Tags as '.$_SESSION["user_id"]. ' with query '.$query;

        echo json_encode($output);
    }

    function fetchData($connect, $received_data, $data){
        $output = [
            'cognitions' => [
                '0' => [
                    'cognition' => [],
                    'tags' => []
                ]
            ],
            'message' => '',
        ];

        //Get cognitions
        $query = "
        SELECT *
        FROM cognitions
        WHERE FK_user in ('".$_SESSION["user_id"]."')";
        
        $statement = $connect->prepare($query);
        
        $statement->execute();

        $index = 0;
        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $output['cognitions'][$index]['cognition'] = $row;
            $index++;
        }

        if(empty($output['cognitions'][0]['cognition'])){
            return [];
        }

        //Get cog_tags
        $query = "
        SELECT *
        FROM cogs_tags
        WHERE FK_cognition in (";
        
        for($i = 0; $i < count($output['cognitions']);$i++){
            $query .= "'".$output['cognitions'][$i]['cognition']['ID']."'";
            if($i != (count($output['cognitions'])-1)){
                $query .= ',';
            }
        }

        $query .= ')';
        
        $statement = $connect->prepare($query);
        
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $connection[] = $row;
        }

        //Get Tags
        $query = "
        SELECT *
        FROM cognition_tags
        WHERE ID in (";

        for($i = 0; $i < count($connection);$i++){
            $query .= "'".$connection[$i]['FK_cognition_tag']."'";
            if($i != (count($connection)-1)){
                $query .= ',';
            }
        }

        $query .= ')';

        $statement = $connect->prepare($query);
        
        $statement->execute();

        while($row = $statement->fetch(PDO::FETCH_ASSOC)){
            $listTags[] = $row;
        }

        //Set everything together
        for($i = 0; $i < count($connection); $i++){
            for($x = 0; $x < count($output['cognitions']); $x++){
                if($connection[$i]['FK_cognition'] == $output['cognitions'][$x]['cognition']['ID']){
                    for($y = 0; $y < count($listTags);$y++){
                        if($connection[$i]['FK_cognition_tag'] == $listTags[$y]['ID']){
                            $output['cognitions'][$x]['tags'][] = $listTags[$y];
                        }
                    }
                }
            }
        }

        return $output;
    }
?>