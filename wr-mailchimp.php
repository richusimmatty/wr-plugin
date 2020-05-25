<?php
/*
Plugin Name: WR Mailchimp
Description: This is used to get user information via form and update to Mailchimp campaign list 
Version:     1.0
Author:      Richu S Immatty
Author URI:  https://www.linkedin.com/in/richusimmatty/
License:     GPL2 etc

Copyright 2020 
(Plugin Name) is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
(Plugin Name) is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License.
*/

include( plugin_dir_path( __FILE__ ) . 'admin/admin.php');

if ( ! function_exists( 'enqueue_jquery_form' ) ) {

add_action('wp_enqueue_scripts', 'enqueue_jquery_form');
function enqueue_jquery_form(){
	wp_enqueue_script('jquery-form');
}
}

add_action('wp_ajax_nopriv_create_applicant','create_applicant');

function load_my_script(){
    wp_register_script( 
        'valdiator', 
        'https://cdn.jsdelivr.net/npm/jquery-validation@1.19.1/dist/jquery.validate.js', 
        array( 'jquery' )
    );
    wp_enqueue_script( 'valdiator' );
}
add_action('wp_enqueue_scripts', 'load_my_script');



function create_applicant(){

  $apiKey = myprefix_get_theme_option( 'input_api' );
  $listId = myprefix_get_theme_option( 'input_listid' );

  $fname = $_POST['fname'];
  $lname = $_POST['lname'];
  $email = $_POST['email'];
  $bday = $_POST['bday'];
  $phone = $_POST['phone'];
  $website = $_POST['website'];

  $status = "subscribed"; // "subscribed","unsubscribed","cleaned","pending"



$memberId = md5(strtolower($data['email']));
  $dataCenter = substr($apiKey,strpos($apiKey,'-')+1);
  $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listId . '/members/' . $memberId;

  $json = json_encode([
      'email_address' => $email,
      'status'        => $status, 
      'merge_fields'  => [
          'FNAME'     => $fname,
          'LNAME'     => $lname,
          'PHONE'     => $phone,
          'BIRTHDAY'  => date('m/d', strtotime($bday)),
          'MMERGE3'   => $website,
 
      ]
  ]);

  $ch = curl_init($url);

  curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
  curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $json);                                                                                                                 

  $result = curl_exec($ch);
  $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  curl_close($ch);

  // return $httpCode;

	wp_send_json_success( $result);
}

// register shortcode
add_shortcode('wrg-subscribe', 'wrform'); 

function wrform(){
  if(!is_page()){
    return false;  }

	?>

<form id="challenge" method="post" action="<?php echo admin_url('admin-ajax.php');?>" name="registration"> 
          <div class="form-group">
    <label for="fname">First Name</label>
<input type="text" name="fname" id="fname">
  </div><br>

         <div class="form-group">
    <label for="lname">Last Name</label>
<input type="text" name="lname"></div><br>

         <div class="form-group">
    <label for="email">Email</label>
<input type="email" name="email">

  </div><br>

           <div class="form-group">
    <label for="bday">Birthday</label>
<input type="date" name="bday">

  </div><br>
             <div class="form-group">
    <label for="phone">Phone</label>
<input type="text" name="phone">

  </div><br>

               <div class="form-group">
    <label for="website">Website</label>
<input type="text" name="website">

  </div>
  

<input type="hidden" name="action" value="create_applicant"><br>
<input type="submit" class="btn btn-primary" value="Submit" style="font-size: 20px;">
      
    </form>
    <style type="text/css">
    	#challenge .error{color:red;}
    </style>
    <span id="apiresult" style="font-size: 18px;text-align: center;display: block;"></span>
        <script type="text/javascript">
      jQuery(document).ready(function($){

      	  $("form[name='registration']").validate({
    // Specify validation rules
    rules: {
      // The key name on the left side is the name attribute
      // of an input field. Validation rules are defined
      // on the right side
      fname: "required",
      lname: "required",
      bday : "required",
      email: {
        required: true,
        // Specify that email should be validated
        // by the built-in "email" rule
        email: true
      },
      phone: {required: true},
      website: "required"

    },
    // Specify validation error messages
    messages: {
      fname: "Please enter your firstname",
      lname: "Please enter your lastname",
      email: "Please enter a valid email address",
      bday: "Please enter date of birth",
      phone: "Please ente your phone number",
      website: "Please ente your website"
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    // submitHandler: function(form) {
    //   form.submit();
    // }
  });



        jQuery('#challenge').ajaxForm({
     
          success: function(response){


            const res = JSON.parse(response.data);
            if(res.status == 400){

            	jQuery("#apiresult").text('Error occured. '+ res.title);
            	jQuery("#apiresult").css('color','red');

            }else{
            	jQuery("#apiresult").text('User subscribed');
            	jQuery("#apiresult").css('color','green');
            	$("#challenge").trigger("reset");
            }
            
          }

        });
   
      });

    </script>
    <?php }?>