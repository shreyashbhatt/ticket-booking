<?php
	require_once('validate_API/dbconnect.php');
	
	$show_timing_id = 1;
	$movie_id =1;
	
	$seat_category_q = "select category,price from seat_category where movie_id=$movie_id";
	$seat_category_r = mysqli_query($con,$seat_category_q);
	while($row = mysqli_fetch_array($seat_category_r,MYSQLI_ASSOC)){
		$category[$row['category']] =$row['price'];
	}
	
	$seat_status_q = "SELECT ti.seat_id FROM ticket_info ti left join booking_info bi on bi.id = ti.booking_id where  bi.movie_id = $movie_id and bi.show_timing_id =  $show_timing_id and status = 1";
	$seat_status_r = mysqli_query($con,$seat_status_q);
	$num_rows = mysqli_num_rows($seat_status_r);
	if($num_rows > 0){
		while($row = mysqli_fetch_array($seat_status_r,MYSQLI_ASSOC)){
			$booked_seat[$row['seat_id']] =$row['seat_id'];
		}
	}
 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="assets/custom.css">
</head>
<body>

<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>Success!</strong><span id='seat_status'> Ticket has been successfully booked.Please check your Email for other information.</span>
  </div>
<div class="container">
  <div class="row">
    <div class="col-sm-12 col-md-12 col-lg-8">
      <div class="card">
        <div class="card-body">
		<p style="text-align: center;">Classic - ₹ 200</p>
			<hr>
          <div class="row">
		  
            <div class="col-sm-1">
              <p>A</p>
              <p>B</p>
              <p>C</p>
              <p>D</p>
             </div>
            <div class="col-sm-11">
			 <p class="seat a1" id="classic_a1"></p>
			 <p class="seat a2" id="classic_a2"></p>
			 <p class="seat a3" id="classic_a3"></p>
			 <p class="seat a4" id="classic_a4"></p>
			 <p class="seat a5" id="classic_a5"></p>
			 <p class="seat a6" id="classic_a6"></p>
			 <p class="seat a7" id="classic_a7"></p>
			 <p class="seat a8" id="classic_a8"></p>
			 <p class="seat a9" id="classic_a9"></p>
			 <p class="seat b1" id="classic_b1"></p>
              <p class="seat b2" id="classic_b2"></p>
			  <p class="seat b3" id="classic_b3"></p>
			  <p class="seat b4" id="classic_b4"></p>
			  <p class="seat b5" id="classic_b5"></p> 
			  <p class="seat b6" id="classic_b6"></p>
			  <p class="seat b7" id="classic_b7"></p>
			  <p class="seat b8" id="classic_b8"></p>
			  <p class="seat b9" id="classic_b9"></p>
			  <p class="seat c1" id="classic_c1"></p>
			  <p class="seat c2" id="classic_c2"></p>
              <p class="seat c3" id="classic_c3"></p>
			  <p class="seat c4" id="classic_c4"></p>
			  <p class="seat c5" id="classic_c5"></p>
			  <p class="seat c6" id="classic_c6"></p> 
			  <p class="seat c7" id="classic_c7"></p>
			  <p class="seat c8" id="classic_c8"></p>
			  <p class="seat c9" id="classic_c9"></p>
			  <p class="seat d1" id="classic_d1"></p>
			  <p class="seat d2" id="classic_d2"></p>
			  <p class="seat d3" id="classic_d3"></p>
              <p class="seat d4" id="classic_d4"></p>
			  <p class="seat d5" id="classic_d5"></p>
			  <p class="seat d6" id="classic_d6"></p>
			  <p class="seat d7" id="classic_d7"></p> 
			  <p class="seat d8" id="classic_d8"></p>
			  <p class="seat d9" id="classic_d9"></p>
			  
             </div>
          </div>
        </div>
      </div>
       <div class="card">
        <div class="card-body">
			<p style="text-align: center;">Royal - ₹ 150</p>
			<hr>
          <div class="row">
            <div class="col-sm-1">
              <p>E</p>
              <p>F</p>
              <p>G</p>
              <p>H</p>
              <p>I</p>
             </div>
            <div class="col-sm-11">
             <p class="seat e1" id="royal_e1"></p>
			 <p class="seat e2" id="royal_e2"></p>
			 <p class="seat e3" id="royal_e3"></p>
			 <p class="seat e4" id="royal_e4"></p>
			 <p class="seat e5" id="royal_e5"></p>
			 <p class="seat e6" id="royal_e6"></p>
			 <p class="seat e7" id="royal_e7"></p>
			 <p class="seat e8" id="royal_e8"></p>
			 <p class="seat e9" id="royal_e9"></p>
			 <p class="seat f1" id="royal_f1"></p>
              <p class="seat f2" id="royal_f2"></p>
			  <p class="seat f3" id="royal_f3"></p>
			  <p class="seat f4" id="royal_f4"></p>
			  <p class="seat f5" id="royal_f5"></p> 
			  <p class="seat f6" id="royal_f6"></p>
			  <p class="seat f7" id="royal_f7"></p>
			  <p class="seat f8" id="royal_f8"></p>
			  <p class="seat f9" id="royal_f9"></p>
			  <p class="seat g1" id="royal_g1"></p>
			  <p class="seat g2" id="royal_g2"></p>
              <p class="seat g3" id="royal_g3"></p>
			  <p class="seat g4" id="royal_g4"></p>
			  <p class="seat g5" id="royal_g5"></p>
			  <p class="seat g6" id="royal_g6"></p> 
			  <p class="seat g7" id="royal_g7"></p>
			  <p class="seat g8" id="royal_g8"></p>
			  <p class="seat g9" id="royal_g9"></p>
			  <p class="seat h1" id="royal_h1"></p>
			  <p class="seat h2" id="royal_h2"></p>
			  <p class="seat h3" id="royal_h3"></p>
              <p class="seat h4" id="royal_h4"></p>
			  <p class="seat h5" id="royal_h5"></p>
			  <p class="seat h7" id="royal_h7"></p> 
			  <p class="seat h8" id="royal_h8"></p>
			  <p class="seat h9" id="royal_h9"></p>
			  <p class="seat i1" id="royal_i1"></p>
			  <p class="seat i2" id="royal_i2"></p>
			  <p class="seat i3" id="royal_i3"></p>
			  <p class="seat i4" id="royal_i4"></p>
              <p class="seat i5" id="royal_i5"></p>
			  <p class="seat i6" id="royal_i6"></p>
			  <p class="seat i7" id="royal_i7"></p>
			  <p class="seat i8" id="royal_i8"></p> 
			  <p class="seat i9" id="royal_i9"></p>
             </div>
          </div>
        </div>
      </div>
    </div>
             <div class="col-sm-4">
               <div class="card info">
                <div class="card-body">
                  <div class="row">
                    <input type="text" class="form-control" placeholder="Enter Full Name" id="usr_name" name="usr_name">
					<label style="color:red" id ="err_name">&nbsp;</label>
					</div>
					
					<div class="row">
						<input type="email" class="form-control" placeholder="Enter Email ID" id="usr_email" name="usr_email">
						<label style="color:red" id ="err_email">&nbsp;</label>
					</div>
					
					<div class="row">
						<input type="mobile" class="form-control" placeholder="Enter Mobile Number" id="usr_mobile" name="usr_mobile">
						<label style="color:red" id ="err_mobile">&nbsp;</label>
					</div>
                    
                    <button type="button" class="btn btn-primary" id="info_btn">Primary</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        

</body>
</html>
<script>
	$(function() {
		var i  = 0;
		var total_fare  = 0;
		var seat,id,split_tkn,category,seat = "";
		var amount_cat = <?php echo json_encode($category) ?>;
		var booked_seat = <?php echo json_encode($booked_seat) ?>;
		$.each(booked_seat, function(key, value) {
		   $('.'+key).removeClass("active");
		   $('.'+key).removeClass("seat");
		   $('.'+key).addClass("booked");
		});
		$('.seat').click(function(){
			id = this.id;
			split_tkn = id.split('_');
			category = split_tkn[0];
			seat = split_tkn[1];
			amount = $('#'+category).val();
			if($(this).hasClass("active")){
				$(this).removeClass("active");
				total_fare -= parseFloat(amount_cat[category]);
			}
			else{
				$(this).addClass("active");
				total_fare += parseFloat(amount_cat[category]);
			}
			if(($('.col-sm-11 p.active').length) > 0)
				$('.info').show();
			else
				$('.info').hide();
		});
		$('#info_btn').click(function(){
			var name = document.getElementById('usr_name');
			var namevalid = /^[a-zA-Z]+ [a-zA-Z]+$/;
			if(!name.value.match(namevalid)){
				$("#err_name").html('Please Enter valid Name');
				name.focus();
				return false; 
			}
			else
				$("#err_name").html('&nbsp;');
			
			var email = document.getElementById('usr_email');
			var emailvalid = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			if(!email.value.match(emailvalid)) {
				$('#err_email').html("Please Enter valid Email ID");
				email.focus(); 
				return false; 
			}
			else
				$("#err_email").html('&nbsp;');
			
			var mobile = document.getElementById('usr_mobile');
			var phoneno = /^[6789]\d{9}$/; 
			if(!mobile.value.match(phoneno)) {
				$("#err_mobile").html('Please Enter valid 10 digit Mobile No');
				mobile.focus(); 
				return false; 
			}
			else
				$("#err_mobile").html('&nbsp;');
			
			var selected_ids = [];
			$(".col-sm-11").find("p.active").each(function(){
				selected_ids.push(this.id);
			});
			$.ajax({
				type: "POST",
				url: 'validate_API/ajax_function.php?f=submit_detail',
				data: {'name': name.value, 'mobile':mobile.value,'email':email.value,'ids':selected_ids},
				dataType: "text",
				success: function(result){
					console.log(result);
					if(result.status == "success"){
						$('.alert-dismissible').show();
					}
					else{
						if (result.error.seat_status != ""){
							$('#seat_status').html(result.error.seat_status);
						}
						if (result.error.email != ""){
							$('#err_email').html(result.error.email);
						}
						if (result.error.mobile != ""){
							$('#err_mobile').html(result.error.mobile);
						}
						if (result.error.name != ""){
							$('#err_name').html(result.error.name);
						}
					}
				} 
			});	
		});
	});
</script>