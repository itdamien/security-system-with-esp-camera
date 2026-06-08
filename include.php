<?php
include("db.php");
$data = mysqli_query($conn, "SELECT COUNT(*) AS total FROM photo");
$total = mysqli_fetch_assoc($data);
$result = mysqli_query($conn, "SELECT *FROM  photo ORDER by id DESC limit 1");
$total= mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to my dashboard</title>
</head>
<body>
    <h1>Smart IOT  Web Dashboard</h1>
    <h2>View All Sensor Records</h2>
   <style>
       h1{
        text-align: center;
        color: greenyellow;
       }
       h2{
        text-align: justify;
        color: aqua;
       }
       table{
        table-layout: auto;
        image-orientation: flip;
        align-items:center;
        border-image: 0%;
        
       }
 
    </style>   
 <table>
    <tr>
         <th>id</th>
         <th>detections</th>
         <th>date</th>
    </tr>

    <?php
      $result = mysqli_query($conn, "SELECT*FROM photo ORDER BY id DESC ");
      while($row=mysqli_fetch_assoc($result)){
    ?>
    <tr>
           <td><?php echo $row ['id'];?></td>
           <td><img src="uploads/<?php echo $row ['photo_name'];?>" alt="" width="10%" height="10%"></td>
            <td><?php echo $row ['date_time'];?></td>
    </tr>
     <?php
     }
     ?>
 </table>
</body>
</html>