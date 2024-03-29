<?php
    session_start();
    if(isset($_SESSION['username'])){
        $username = $_SESSION['username'];
    } else {
        header("Location: /HTML/index.php");
    }

    //connect to the database
    $db = mysqli_connect("localhost", "root", "", "test");

    
    if(mysqli_connect_errno()){
        echo "Failed to connect to MySQL: " . mysqli_connect_error(); //Shows an error message if the connection failes
    } else {

        //gets the item from the form
        if(isset($_POST['item'])){
            $item = $_POST['item'];
            //finds the same user by username
            $query = "SELECT * FROM registration  WHERE username = '$username'";
            $result = mysqli_query($db, $query);
            $row = mysqli_fetch_array($result);
            $itemDatabase = $row['item'];
            echo "<h2>$itemDatabase</h2>";

            //gets json array from database and assign to variable
            $json = json_decode($itemDatabase, true);
            //adds item to json array
            $json[] = $item;
            $json = json_encode($json); //encode array to json
            //updates the item in the database
            $query = "UPDATE registration  SET item = '$json' WHERE username = '$username'";
            $result = mysqli_query($db, $query);

            //goes to page
            header("Location: todo.php");

        }
      }

    //clear button
    if(isset($_POST['clear'])){
        $item = null;
        $query = "UPDATE registration  SET item = '$item' WHERE username = '$username'";
        $result = mysqli_query($db, $query);
    }

    //delete button
    if(isset($_POST['delete'])){
      //gets the id of the item to delete
      $id = $_POST['id'];
      //gets the item from the database
      $query = "SELECT * FROM registration  WHERE username = '$username'";
      $result = mysqli_query($db, $query);
      $row = mysqli_fetch_array($result);
      $itemDatabase = $row['item'];


      //gets the json array from database
      $json = json_decode($itemDatabase, true); //When set to true, the returned object will be converted into an associative array.
      //removes item from array
      unset($json[$id]);
      //reindex array
      $json = array_values($json); //this is done so that the array starts from 0 again. This is important because the array is used as a key in the json array.
      //encodes json array
      $json = json_encode($json);
      //updates the item in the database
      $query = "UPDATE registration  SET item = '$json' WHERE username = '$username'";
      $result = mysqli_query($db, $query);

      //go to url
      header("Location: todo.php");
    }

    //edit button
    if(isset($_POST['edit'])){
      //gets the id of the item to edit
      $id = $_POST['id'];
      //gets the item from the database
      $query = "SELECT * FROM registration  WHERE username = '$username'";
      $result = mysqli_query($db, $query);
      $row = mysqli_fetch_array($result);
      $itemDatabase = $row['item'];

      //gets json array from database and assign to variable
      $json = json_decode($itemDatabase, true);


      echo "<div class='modal'>";
      echo "<form class='modalcontent' id=editForm action='todo.php' method='post'>";
      echo "<input autocomplete='off' type='text' name='item' value='$json[$id]' required>";
      echo "<input type='hidden' name='id' value='$id'>";
      echo "<input type='submit' name='submit' value='Submit'>";
      echo "<a href='todo.php' class='modalclose'>&times;</a>";
      echo "</form>";
      echo "</div>";
    }

    if(isset($_POST['submit'])){
      //gets the id of the item to edit
      $id = $_POST['id'];

      //gets json array from database and assign to variable
      $json = json_decode($itemDatabase, true);

      $item = $_POST['item'];
      //updates the item in the array
      $json[$id] = $item;
      $json = json_encode($json);
      //updates the item in the database  
      $query = "UPDATE registration  SET item = '$json' WHERE username = '$username'";
      $result = mysqli_query($db, $query);
      //go to url
      header("Location: todo.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>item local storage</title>
    <link rel="stylesheet" href="/CSS/test.css" />
  </head>
  <body>

    <div class="topnav">
      <a class="active">Home</a>
      <?php echo "<a> Welcome $username </a> " ?>
      <a href="/PHP/logout.php">Logout</a>
    </div>

    <main>
      <form id="itemForm" action="/HTML/todo.php" method="post" autocomplete="off">

        <input type="text" placeholder="Item name" name="item" required/>
        <button class="btnSubmit">Add item</button>

      </form>

      <form id="clearForm" action="/HTML/todo.php" method="post" autocomplete="off">
        <button class="btnClear" name="clear" >Clear list</button>
      </form>

      <div class="items" action='/HTML/todo.php' method='post' >
        <?php
          $db = mysqli_connect("localhost", "root", "", "test");

          if(mysqli_connect_errno()){
              echo "Failed to connect to MySQL: " . mysqli_connect_error(); //Shows an error message if the connection failes
          } else {
            $query = "SELECT * FROM registration  WHERE username = '$username'";
            $result = mysqli_query($db, $query);
            $row = mysqli_fetch_array($result);
            $item = $row['item'];
            $item = json_decode($item, true);
            if($item == "" || $item == null){
              echo "<h2>No item</h2>";
            } else {
              $i = 0;
              foreach($item as $item){
                echo "<form action='/HTML/todo.php' id='$i' method='post' >";
                echo "<h2>$item</h2>";
                echo "<button class='btnDelete' id='$i' name='delete'>Delete</button>";
                echo "<input type='hidden' name='id' value='$i' />";
                echo "<button href='modalbox' class='btnEdit' id='$i' name='edit'>Edit</button>";
                echo "</form>";
                $i = $i + 1; //increments the counter

              }
            }
          }
        ?>
      </div>
    </main>
  </body>
</html>
