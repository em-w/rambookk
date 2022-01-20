<?php
session_start();

 // read json file into array of strings
 $jsonstring = file_get_contents("userprofiles.json");
 
 // save the json data as a PHP array
 $userarray = json_decode($jsonstring, true);

 $userstring = ""; // string containing the contents of user's file (not in php array format)

 $likedposts = $userarray[$_SESSION["userUid"] - 1]["likedPosts"];

 $postsrequested = true;
 
 // use GET to determine type of access
 if (isset($_GET["access"])){
  $access = $_GET["access"];
 } else {
  $access = "all"; 
 }
 
  // pull student, alumnus or staff only or return all
  $returnData = [];
  if ($access != "all") { 
      if ($access == "self") {
         $userstring = file_get_contents($_SESSION["userUid"] . ".json");
         $userposts = json_decode($userstring);
         if ($userposts != null) {
            $returnData = $userposts;
         }
      }

      else if ($access == "allpfs") {
         $postsrequested = false;
         foreach ($userarray as $user) {
            if ($user["uid"] == $_SESSION["userUid"]) {
               $user["current"] = true;
            } else {
				$user["current"] = false;
			}
            $returnData[] = $user;
         }
      }

      else if ($access == "following") {
         $following = $userarray[$_SESSION["userUid"] - 1]["following"];
         if ($following != null) {
               foreach ($following as $userUid) {
                  $userstring = file_get_contents($userUid . ".json");
                  $userposts = json_decode($userstring, true);
                  if ($userposts != null) {
                  foreach ($userposts as $post) {
                     $returnData[] = $post;
                  } 
               }
            }
        

         }
      }
	  
	  else if (is_numeric($access)) {
		 $userstring = file_get_contents($access . ".json");
         $userposts = json_decode($userstring);
         if ($userposts != null) {
            $returnData = $userposts;
         }
		
	  }

     else if ($access == "liked") {
        foreach ($userarray as $user) {
           $userstring = file_get_contents($user["uid"] . ".json");
           $userposts = json_decode($userstring, true);
           if ($userposts != null) {
              foreach ($userposts as $post) {
                  foreach ($likedposts as $likedpost) {
                     if ($post["uid"] == $likedpost) {
                        $returnData[] = $post;
                     }
                  } // foreach
              } // foreach
           } // if
        } // foreach
     } // if

   // if access == all
  } else {
      foreach ($userarray as $user) {
         $userstring = file_get_contents($user["uid"] . ".json");
         $userposts = json_decode($userstring, true);
         if ($userposts != null) {
            foreach ($userposts as $post) {
               $returnData[] = $post;
            } 
         }
      }
  }

if ($postsrequested) {
   foreach ($returnData as $post) {
      foreach ($likedposts as $likedpost) {
         if ($post["uid"] == $likedpost) {
            $post["liked"] = true;
         } else {
            $post["liked"] = false;
         }
      }
   }
}


// encode the php array to json 
 $jsoncode = json_encode($returnData, JSON_PRETTY_PRINT);
 echo ($jsoncode);



?>