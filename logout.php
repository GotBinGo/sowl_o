<?php
session_start();
if(isset($_SESSION['views']))
{
  unset($_SESSION['views']);
  echo "logged out";
  echo "<meta http-equiv='refresh' content='0; url=http://bordak.eu/sowl'/>";
}
else
{
echo "not log in";
}
?>