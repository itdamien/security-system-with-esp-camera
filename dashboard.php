<?php

include("db.php");

$data = mysqli_query($conn, "SELECT COUNT(*) AS total FROM sensor_log");

$total = mysqli_fetch_assoc($data);

$result = mysqli_query($conn, "SELECT * FROM sensor_log ORDER BY id DESC LIMIT 3");

$latest = mysqli_fetch_assoc($result);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=3">
    <meta http-equiv="refresh" content="5">
    <title>ESP-CAM Security Dashboard</title>
</head>
<body>
   <h1>ESP-CAM Security Dashboard</h1>
  
    <div class="card">
        <h3>Total Records</h3>
        <h2>
            <?php echo $total['total']; ?>
        </h2>
    </div>

    <div class="card">
        <h3>Latest Distance</h3>
        <h2>
            <?php if($latest){
              echo $latest['distance'];
            }else{
              echo "No Data";
           }?>
        </h2>
    </div>

    <div class="card">
        <h3>Status</h3>
        <h2>
            <?php if($latest){
              echo $latest['led_status'];
            }else{
              echo "No Data";
           }?>
        </h2>
    </div>

    <h2>Sensor Logs</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Distance</th>
            <th>Status</th>
            <th>Email Status</th>
            <th>Date</th>
        </tr>

        <?php 
        $result = mysqli_query($conn, "SELECT * FROM sensor_log ORDER BY id DESC");

        while($row=mysqli_fetch_assoc($result)){
        ?>
        
       <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['distance']; ?></td>
            <td><?php echo $row['led_status']; ?></td>
            <td><?php echo $row['email_status']; ?></td>
            <td><?php echo $row['date_time']; ?></td>
        </tr>
        <?php
        }
        ?>
    </table>

    

    <h2>Captured Photos</h2>

    <?php 
    $photos = mysqli_query($conn,"SELECT * FROM photo ORDER BY id DESC");

    while($photo=mysqli_fetch_assoc($photos)){
    ?>

    <img src="uploads/<?php echo $photo['photo_name'];?>" >

    <?php } ?>
</body>
<footer>
    Smart Security Monitoring System © 2026 | Developed by Don Prince & call me oliviss | All Rights Reserved
</footer>

</html>

