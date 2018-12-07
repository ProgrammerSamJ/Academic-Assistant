<?php
//load_data.php
$connect = mysqli_connect("localhost", "root", "");
$output = '';
if(isset($_POST["class"]))
{
     if($_POST["class"] != '')
     {
          $sql = "SELECT * FROM classes WHERE class = '".$_POST["class"]."'";
     }
     else
     {
          $sql = "SELECT * FROM classes";
     }
     $result = mysqli_query($connect, $sql);
     while($row = mysqli_fetch_array($result))
     {
          $output .= '<div class="col-md-3"><div style="border:1px solid #ccc; padding:20px; margin-bottom:20px;">'.$row["work_type"].'</div></div>';
     }
     echo $output;
}
?>
