<?php
session_start();
if(isset($_SESSION['views']))
{
  unset($_SESSION['views']);
  echo "logged out";
}
else
{
echo "not log in";
}
?>
