<?php

  session_start();
  var_dump($_SESSION);
  $filename = "data.txt";

  // when user is logged in
  if (isset($_SESSION["logged"])) {
    //when user wants to delete
    if($_GET['delete'] != "") {
      // array
      $row = file($filename);
      unset($row[$_GET['delete']]);
      // to open file and reset content
      $handler = fopen($filename, "w"); //w = write
      foreach($row as $key => $userdata){
          // to rewrite all the other rows
          fwrite($handler, $userdata);
          }
      fclose($handler);
      // end of session
      session_destroy();
      echo "user deleted!";
      ?>

      <br>
      <a href="index.php">BACK TO HOMEPAGE</a>

      <?php
      exit;
    }

    if ($_GET["logout"] === "") {
      session_destroy();
      // redirect to homepage AFTER logout
      header("Location: index.php");
    }
    $key = $_SESSION["userKey"];
    echo "User Logged";
    ?>

    <a href="index.php?logout">LOGOUT</a>
    <br>
    <a href="index.php?delete=<?php echo $key;?>">LOGOUT & DELETE</a>

    <?php
  } else {

    // when user is not registered:
    if ($_GET["register"] === "") {
      $usernameReg = $_POST["username"];
      $mailReg = $_POST["mail"];
      $passwordReg = $_POST["password"];

      // to avoid blank forms
      if( $usernameReg != '' && $mailReg != '' && $passwordReg != '') {

        //check username
        $row = file($filename);

        $userFound = false;

        foreach ($row as $key => $userdata) {
          $userdataArray = explode(",", $userdata);

          if ($usernameReg == $userdataArray[0]) {
            $userFound = true;
            echo "this user already exist";
          }

        }

        if (!$userFound) {
          // check email
          $at = strpos($mailReg, '@');
          // to check there is a dot AFTER $at
          $dot = strpos($mailReg, '.', $at);

          if ($dot !== false && $at !== false) {
            $verifiedMail = true;
          } else {
            $verifiedMail = false;
            echo "error! please check your mail! <br>";
          }

          // to chec if password has at least 8 characters

          if (strlen($passwordReg) > 7) {
            $verifiedPassword = true;
          } else {
            $verifiedPassword = false;
            echo "your password must be at least 8 characters long <br>";
          }

          if ($verifiedMail == true && $verifiedPassword == true) {
            // to write in database (data.txt)
            $handler = fopen($filename, "a+"); // a+ = open for reading/writing
            fwrite($handler, "$usernameReg, $mailReg, $passwordReg\n");
            // \n= new line
            $_SESSION["logged"] = true;
            exit("Well done. You are now registered! <br>");
          }
        }

      } else {
        echo "You have to fill all forms!";
      }

    }

    //when user wants to log in
    if ($_GET["login"] === "") {
      $usernameLog = $_POST["username"];
      $passwordLog = $_POST["password"];

      $row = file($filename);

      $userFound = false;

      foreach ($row as $key => $userdata) {
        list($username, $mail, $password) = explode(", ", $userdata);
        $password = trim($password);

        if ($usernameLog == $username && $passwordLog == $password) {
          $userFound = true;

          $_SESSION["logged"] = true;
          $_SESSION["userKey"] = $key; //key = the row in my text file
          exit("Authorized Access");
        }

      }

      if (!$userFound) {
        echo "Error! Check again username and password";
      }
    }


    ?>

    <!DOCTYPE html>
    <html lang="en" dir="ltr">
    <head>
      <meta charset="utf-8">
      <title>User Session</title>
    </head>

    <body>

      <h3>New Account:</h3>
      <form action="index.php?register" method="post">
        <label for="username">username</label>
        <input type="text" name="username" />
        <label for="mail">mail</label>
        <input type="text" name="mail" />
        <label for="password">password</label>
        <input type="text" name="password" />

        <input type="submit" value="create" />
      </form>

      <hr>

      <h3>Log In:</h3>
      <form action="index.php?login" method="post">
        <label for="username">username</label>
        <input type="text" name="username" />
        <label for="password">password</label>
        <input type="text" name="password" />

        <input type="submit" value="login" />
      </form>

    </body>
    </html>

<!-- php closing (from row 47)-->
<?php
}
 ?>
