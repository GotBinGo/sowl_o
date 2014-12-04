<?php
session_start();
//var_dump($_SESSION);
require_once('Facebook/FacebookSession.php');
require_once('Facebook/FacebookRedirectLoginHelper.php');
require_once('Facebook/FacebookRequest.php');
require_once('Facebook/FacebookResponse.php');
require_once('Facebook/FacebookSDKException.php');
require_once('Facebook/FacebookRequestException.php');
require_once('Facebook/FacebookAuthorizationException.php');
require_once('Facebook/GraphObject.php');
require_once('Facebook/GraphUser.php');
require_once('Facebook/GraphSessionInfo.php');

require_once( 'Facebook/HttpClients/FacebookHttpable.php' );
require_once( 'Facebook/HttpClients/FacebookCurl.php' );
require_once( 'Facebook/HttpClients/FacebookCurlHttpClient.php' );
require_once( 'Facebook/Entities/AccessToken.php' );
require_once( 'Facebook/Entities/SignedRequest.php' );




use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphSessionInfo;


FacebookSession::setDefaultApplication( '1438024856452524','e378f2e134ba0b2a077712e25e87ba50' );

// login helper with redirect_uri

    $helper = new FacebookRedirectLoginHelper('http://bordak.eu/sowl/test.php' );

try {
  $session = $helper->getSessionFromRedirect();
} catch( FacebookRequestException $ex ) {
  // When Facebook returns an error
} catch( Exception $ex ) {
  // When validation fails or other local issues
}

// see if we have a session
if ( isset( $session ) ) {
  // graph api request for user data
  $request = new FacebookRequest( $session, 'GET', '/me' );
  $response = $request->execute();
  // get response
  $graphObject = $response->getGraphObject(GraphUser::className());
  $fbid = $graphObject->getId();
  echo $fbid;
  if($fbid != "")
  {
    include("conn.php");
    $result = mysqli_query($conn,"SELECT id, name FROM users WHERE fbid='$fbid'");
    mysqli_close($conn);
    if(mysqli_num_rows($result) == 1)
    {
      $row = mysqli_fetch_array($result);
      $_SESSION['views']=array($row[0],$row[1]);
      //echo "logged id";
    }
  }

  // print data
  //echo '<pre>' . print_r( $graphObject, 1 ) . '</pre>';
} else {
  // show login url
  echo '<a href="' . $helper->getLoginUrl() . '">Facebook Login</a>';
}
//echo "end";
?>