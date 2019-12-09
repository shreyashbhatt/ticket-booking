<?php
	
	if(function_exists($_REQUEST['f'])) {
        $_REQUEST['f']();
    }
	function test_input($data) {
	  $data = trim($data);
	  $data = stripslashes($data);
	  $data = htmlspecialchars($data);
	  return $data;
	}
	function submit_detail(){
		require_once('dbconnect.php');
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$Err = [];
			$name = test_input($_POST["name"]);
			$email = test_input($_POST["email"]);
			$mobile = test_input($_POST["mobile"]);
			$show_id = 1;
			
			if (empty($name)) {
				$Err['name'] = "Name is required";
			} else {
				if (!preg_match("/^[a-zA-Z ]*$/",$name)) {
				  $Err['name'] = "Only letters and white space allowed in Name";
				}
			}
			if (empty($email)) {
				$Err['email'] = "Email is required";
			}
			else{
				if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				  $Err['email'] = "Invalid email format";
				}
			}
			if (empty($mobile)) {
				$Err['mobile'] = "Mobile No. is required";
			}
			else{
				if (!preg_match('/^[6-9]\d{9}$/',$mobile)) {
				  $Err['mobile'] = "Please Enter valid 10 Digit Mobile Number";
				}
			}
			if(count($Err) == 0){
				$ticket_detail_q = "";
				$amount  = 0;
				$seating_arrangment = "";
				$qty=0;
				$movie_id = 1; //hardcoded movie_id as we dont have any specific info
				$show_timing_id = 1;
				foreach($_POST['ids'] as $key=>$value){
					$ticket = explode("_",$value);
					$ticket_detail_q .="('$booking_id','$ticket[1]'),"; 
					$amount +=$category[$ticket[0]];
					$seating_arrangment .= $ticket[1];
					$seats .= "'".$ticket[1]."',";
					$qty++;
				}
				$seats = rtrim($seats,",");
				$seat_status_q = "SELECT ti.seat_id FROM `ticket_info` ti left join booking_info bi on bi.id = ti.booking_id where ti.seat_id in ($seats) and bi.movie_id = $movie_id and bi.show_timing_id =  $show_timing_id";
				$seat_status_r = mysqli_query($con,$seat_status_q);
				$num_rows = mysqli_num_rows($seat_status_r);
				if($num_rows > 0){
					$message['status'] = "fail";
					$message['error'] = 
					$Err['seat_status'] = "Sorry, the seat which you are trying to book is already booked.";
					$message['error'] = $Err;
					return $message;
				}
				
				$seat_category_q = "select category,price from seat_category where movie_id=$movie_id";
				$seat_category_r = mysqli_query($con,$seat_category_q);
				while($row = mysqli_fetch_array($seat_category_r,MYSQLI_ASSOC)){
					$category[$row['category']] =$row['price'];
				}
				
				$show_info_q = "SELECT mi.name,si.show_time FROM movie_info mi left join show_info si on si.movie_id =  mi.id where mi.id = 1 and show_time ='19:00:00'";
				$show_info_r = mysqli_query($con,$show_info_q);
				while($row = mysqli_fetch_array($show_info_r,MYSQLI_ASSOC)){
					$movie_name = $row['name'];
					$show_time = $row['show_time'];
				}
				
				$booking_info_q = "insert into booking_info(name,email,mobile,booking_date,show_timing_id,status) values('$name','$email','$mobile',now(),'$show_id','1')";
				$booking_info_r = mysqli_query($con,$booking_info_q);
				$booking_id = mysqli_insert_id($con);
				
				if(strlen($ticket_detail_q) > 0){
					$ticket_detail_q = rtrim($ticket_detail_q,",");
					$seating_arrangment = rtrim($seating_arrangment,",");
					mysqli_query($con,"insert into ticket_info(booking_id,seat_id) values $ticket_detail_q");
				}
				mysqli_close($con);
				
				$email_message = format_email($booking_id,$movie_name,$show_time,$qty,$amount,$discount=0,$amount,$seating_arrangment);
				//send_mail($email, "Your Ticket", $email_message);
				$message['status'] = 'success';
			}
			else{
				$message['status'] = 'fail';
				$message['error'] = $Err;
			}
			echo json_encode($message);
		}
	}
	function send_mail($to, $subject, $email_message){
		$headers = 'From: no-reply@example.com' . "\r\n" .
			'Reply-To: webmaster@example.com' . "\r\n" .
			'X-Mailer: PHP/' . phpversion();
		mail($to, $subject, $email_message, $headers);
	}
	function seat_status(){
		if ($_SERVER["REQUEST_METHOD"] == "POST") {
			$seat_id = $_POST['seat_id'];
			$Err['status'] = "success";
			if(empty($seat_id)) {
				$Err['status'] = "fail";
				$Err['msg'] = "Blank Response";
			} else {
				if (!preg_match("/^[a-z0-9]*$/",$name)) {
					$Err['status'] = "fail";
					$Err['msg'] = "Invalid Seat";
				}
			}
			
			if($Err['status'] == "success"){
				//whether ip is from share internet
				if (!empty($_SERVER['HTTP_CLIENT_IP']))   
				{
					$ip_address = $_SERVER['HTTP_CLIENT_IP'];
				}
				//whether ip is from proxy
				elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))  
				{
					$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				//whether ip is from remote address
				else
				{
					$ip_address = $_SERVER['REMOTE_ADDR'];
				}
				$seat_status_q = "select * from seat_status where seat_id = $seat_id and";
				$booking_info_r = mysqli_query($con,$booking_info_q);
				$booking_id = mysqli_insert_id($con);
				foreach($_POST['ids'] as $key=>$value){
					$ticket = explode("_",$value);
					$ticket_detail_q .="('$booking_id','$ticket[1]'),"; 
					$amount +=$category[$ticket[0]];
					$seating_arrangment .= $ticket[1];
					$qty++;
				}
			}
		}
	}
	function format_email($booking_id,$movie_name,$show_time,$qty,$amt,$discount,$amount_paid,$seating_arrangment){
		$current_time = date("Y-m-d H:i");
		$current_time = date("d M,Y", strtotime($current_time));
		
		$show_time = date("h:i A | d M, Y", strtotime($show_time));
		return $html = "<tbody>
   <tr>
      <td align='center' style='padding:0px 25px 10px 25px;font-size:16px;font-family:Arial,sans-serif;text-align:center;vertical-align:top;background-color:#ffffff;color:#828282'>
         <span style='font-size:22px;font-weight:bold;color:#4caf50'>Your booking is confirmed!</span>
      </td>
   </tr>
   <tr>
      <td align='center' style='padding:5px 25px 5px 25px;font-size:16px;font-family:Arial,sans-serif;text-align:center;vertical-align:top;background-color:#ffffff;color:#828282'>Booking ID <span style='color:#000;font-weight:bold'>$booking_id</span></td>
   </tr>
   <tr>
      <td align='center' style='width:600px;padding:25px 10px 0 10px;text-align:left;background-color:#ffffff'>
         <table align='center' cellpadding='0' cellspacing='0' style='width:580px;margin:0 auto;background-color:#ffffff'>
            <tbody>
               <tr>
                  <td>
                     <table align='center' cellpadding='0' cellspacing='0' style='width:580px;margin:0 auto;background-color:#f5f5f5;border-radius:5px'>
                        <tbody>
                           <tr>
                              <td>
                                 <table align='center' cellpadding='0' cellspacing='0' style='width:580px;margin:0 auto;padding:10px;background-color:#f5f5f5;border-top-left-radius:5px;border-top-right-radius:5px'>
                                    <tbody>
                                       <tr>
                                          <td valign='top' align='center' style='width:370px;background-color:#f5f5f5;padding:10px'>
                                             <table cellspacing='0' cellpadding='0' align='center' style='width:100%;background-color:#f5f5f5;margin:0px auto'>
                                                <tbody>
                                                   <tr>
                                                      <td valign='top' align='left' style='padding:0px 5px 0px 0px;height:50px;font-size:20px;font-weight:bold;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#f5f5f5;color:#3c3c3c'>
                                                         <span>$movie_name</span>
                                                      </td>
                                                   </tr>
                                                   <tr>
                                                      <td valign='bottom' align='left' style='padding:0px 5px 0px 0px;font-size:16px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#f5f5f5;color:#3c3c3c'>
                                                         $show_time<br>
                                                         <span style='display:block;font-size:13px;color:#828282;font-weight:400;padding-top:10px'>
                                                            <span>Cinepolis: V3S Mall, Laxmi Nagar (SCREEN 3)</span><br>National Capital Region (NCR), Delhi
                                                            <div></div>
                                                         </span>
                                                      </td>
                                                   </tr>
                                                </tbody>
                                             </table>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>
               <tr>
                  <td valign='top' style='width:580px;background-color:#f5f5f5;border:0'>
                     <table cellspacing='0' cellpadding='0' align='center' style='width:100%;background-color:#f5f5f5;margin:0px auto;border:0'>
                        <tbody>
                           <tr>
                              <td valign='top' align='center' style='width:582px;background:url(https://ci4.googleusercontent.com/proxy/hEvK2_lgIUiZ2RI2C7b5xe5Mqv8JLIPwECZZLXDKXmZmWa8VpfFnnAKEKvbdtv_lcDqi0hThelaKT7CpJ-k4imlAw4HorCnKBB06=s0-d-e1-ft#https://in.bmscdn.com/webin/mailer/dotted-line-new-5.png) no-repeat;background-repeat:no-repeat;background-position:top;overflow:visible;border:0;background-size:contain'>
                                 <table cellspacing='0' cellpadding='0' align='center' style='width:580px;margin:0px auto;border:0'>
                                    <tbody>
                                       <tr>
                                          <td valign='top' align='left' style='width:60px;height:40px;padding:40px 30px 0px 30px;vertical-align:middle;text-align:center;font-size:26px;font-weight:800;font-family:Arial,sans-serif;color:#000000;border:0'>
                                             <p style='margin:0;padding-bottom:6px;line-height:0;border:0'>1</p>
                                             <span style='font-size:13px;color:#828282;font-weight:400'>Ticket</span>
                                          </td>
                                          <td valign='top' align='left' style='width:270px;height:40px;padding:20px 10px 0px 15px;vertical-align:middle;font-family:Arial,sans-serif;text-align:left;font-size:13px;color:#828282;font-weight:400;border:0'>
                                             <p style='margin:0;padding-bottom:6px;border:0'>SCREEN 3</p>
                                             <span style='font-size:15px;font-weight:bold;color:#010101'>$seating_arrangment</span>
                                          </td>
                                          <td valign='top' style='width:120px;border:0'>
                                             <img src='https://ci3.googleusercontent.com/proxy/sUv2f7OSPFQwWy4xQFLaDkxkgmfhVzGNHuXJ3qcl0E3MoHN7bEx9th16b-_cQOiVOcAegqE0AE48LAq55KXVdFBeDpPjkFOy8A9LSGW14UnN4Hmcr0k=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/161202ticket/bookingstamps.png' width='105' height='107' style='display:block;color:#010101' border='0' class='CToWUd'>
                                          </td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </td>
                           </tr>
                           <tr>
                              <td colspan='2' valign='top' align='center' style='background-color:#ffffff;padding-bottom:10px'>
                                 <img src='https://ci4.googleusercontent.com/proxy/TYwV5nsuNJixJOZs5d6_wwdGyemklPrRqjTMgT-rFmR-4aup1KzR-krc3DjiuoZMGSS7tGv97HhjrUAJ6EbJqIOoCKjqeFb_JrF8VROzoQ2T=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/161223confirmation/4a.png' width='580' height='8' style='display:block;background-color:#f5f5f5;color:#010101' border='0' class='CToWUd'>
                              </td>
                           </tr>
                        </tbody>
                     </table>
                  </td>
               </tr>
            </tbody>
         </table>
      </td>
   </tr>
</tbody>
</table>
</td>
</tr>
<tr>
   <td align='left' style='text-align:center;background-color:#ffffff;padding:20px'>
   </td>
</tr>
</tbody></table>
</td>
</tr>
<tr>
   <td valign='top' align='left' style='font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#828282'>
      <table align='center' cellpadding='0' cellspacing='0' style='width:600px;font-family:Arial,sans-serif;margin:0 auto'>
         <tbody>
            <tr>
               <td align='left' style='width:580px;padding:15px 10px 15px 10px;font-size:13px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;font-weight:600;color:#828282;letter-spacing:1px'>ORDER SUMMARY</td>
            </tr>
         </tbody>
      </table>
   </td>
</tr>
<tr>
   <td align='left' style='text-align:center;background-color:#ffffff'>
      <table cellpadding='0' cellspacing='0' style='width:580px;padding:25px 25px 15px 25px;border:2px solid #f1f1f1;border-bottom:0px!important;border-top-left-radius:5px;border-top-right-radius:5px;font-family:Arial,sans-serif;margin:0 auto'>
         <tbody>
            <tr>
               <td valign='top' align='left' style='width:100%;font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#828282'>
                  <table cellpadding='0' cellspacing='0' style='width:100%;padding-bottom:20px;font-family:Arial,sans-serif;margin:0 auto'>
                     <tbody>
                        <tr>
                           <td align='left' style='width:50%;font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#3f474e'>
                              <p style='margin:0;padding:0 0 12px 0'>
                                 <strong>TICKET AMOUNT</strong>
                              </p>
                              <span style='color:#828282;font-size:13px'>Quantity</span>
                           </td>
                           <td align='right' style='width:50%;font-size:15px;font-family:Arial,sans-serif;text-align:right;vertical-align:top;background-color:#ffffff;color:#3c3c3c'>
                              <p style='margin:0;padding:0 0 12px 0'>Rs.$amt</p>
                              <span style='color:#828282;font-size:13px'>$qty<span> ticket </span></span>
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td valign='top' align='left' style='width:100%;font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#828282'>
                  <table cellpadding='0' cellspacing='0' style='width:100%;padding:20px 0px;border-top:2px dashed #f1f1f1;font-family:Arial,sans-serif;margin:0 auto'>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td valign='top' align='left' style='width:100%;font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#828282'></td>
            </tr>
            <tr></tr>
            <tr></tr>
            <tr>
               <td valign='top' align='left' style='width:100%;font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#828282'>
                  <table cellpadding='0' cellspacing='0' style='width:100%;padding:20px 0px 0px 0px;font-family:Arial,sans-serif;margin:0 auto'>
                     <tbody>
                        <tr>
                           <td align='left' style='font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#3f474e'>
                              <strong>DISCOUNT</strong>
                           </td>
                           <td align='right' style='font-size:15px;font-family:Arial,sans-serif;text-align:right;vertical-align:top;background-color:#ffffff;color:#3c3c3c'>Rs.$discount</td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </td>
</tr>
<tr>
   <td align='center' style='width:580px;text-align:center;background-color:#ffffff'>
      <table cellpadding='0' cellspacing='0' style='width:580px;margin:0 auto;background-color:#ffffff'>
         <tbody>
            <tr>
               <td valign='top' align='left' style='font-size:14px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#f1f1f1;color:#828282'>
                  <table cellpadding='0' cellspacing='0' style='width:100%;padding:20px 25px 10px 25px;border-bottom:2px dashed #f1f1f1;font-family:Arial,sans-serif;margin:0 auto'>
                     <tbody>
                        <tr>
                           <td align='left' style='font-size:16px;font-family:Arial,sans-serif;text-align:left;vertical-align:top;font-weight:bold;background-color:#f1f1f1;color:#333333'>AMOUNT PAID</td>
                           <td align='left' style='font-size:16px;font-family:Arial,sans-serif;text-align:right;vertical-align:top;font-weight:bold;background-color:#f1f1f1;color:#333333'>Rs.$amount_paid</td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
            <tr>
               <td valign='top' style='width:100%;background-color:#ffffff'>
                  <img src='https://ci6.googleusercontent.com/proxy/TYWLRM9skOXuDsYDKbAxTem23y0_04oW3z4Qt-FiFSTz2xmj7W0ZKqB-RMEtab0bjFlRPKgDdIQnOweNwooGLnh9lHHbVhs32SYB-oPuZQ=s0-d-e1-ft#https://in.bmscdn.com/mailers/images/161202ticket/zigzag.png' width='580' height='15' style='width:100%;display:block;background-color:#ffffff;color:#010101' border='0' class='CToWUd'>
               </td>
            </tr>
            <tr>
               <td valign='top' align='center' style='width:580px;padding:20px 0;background-color:#ffffff'>
                  <table cellspacing='0' cellpadding='0' align='center' style='width:100%;background-color:#ffffff;margin:0px auto'>
                     <tbody>
                        <tr>
                           <td valign='top' align='left' style='width:260px;font-size:13px;font-weight:bold;font-family:Arial,sans-serif;text-align:left;vertical-align:top;background-color:#ffffff;color:#3f474e'>
                              <p style='color:#787878;font-size:12px;margin:0;padding:0 0 4px 0'>Booking Date &amp; Time</p>
                              $current_time
                           </td>
                        </tr>
                     </tbody>
                  </table>
               </td>
            </tr>
         </tbody>
      </table>
   </td>
</tr>
</tbody>";
	}
?>