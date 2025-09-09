<?php
    $conn = mysqli_connect("localhost","root","","testDatabase");
    if(!$conn)
    {
        trigger_error('Could not Connect' .mysqli_connect_error());
    }

    $sql = "SELECT * FROM Registration";
    $result = mysqli_query($conn, $sql);

    $array = array();

    while($row=mysqli_fetch_assoc($result))
    {
        $array[] = $row;
    }

    echo'{"data":'.json_encode($array).'}'; 
  ?>