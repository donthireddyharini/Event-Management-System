<?php
if(!isset($_SESSION)) {
    session_start();
}
$b_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : (isset($name) ? $name : '');
$b_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : (isset($email) ? $email : '');
$b_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>
<div class="container-fluid">
	<form action="" id="manage-book">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<input type="hidden" name="venue_id" value="<?php echo isset($_GET['venue_id']) ? $_GET['venue_id'] :'' ?>">
		<input type="hidden" name="user_id" value="<?php echo $b_user_id ?>">
		<div class="form-group">
			<label for="" class="control-label">Full Name</label>
			<input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($b_name) ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Address</label>
			<textarea cols="30" rows="2" required="" name="address" class="form-control"><?php echo isset($address) ? $address :'' ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($b_email) ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Contact #</label>
			<input type="text" class="form-control" name="contact" value="<?php echo isset($contact) ? $contact :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Duration</label>
			<input type="text" class="form-control" name="duration" placeholder="e.g., 4 hours, 1 day" value="<?php echo isset($duration) ? $duration :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Desired Event Schedule</label>
			<input type="text" class="form-control datetimepicker" name="schedule" value="<?php echo isset($schedule) ? $schedule :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Event Description / Special Requirements</label>
			<textarea cols="30" rows="3" name="description" class="form-control" placeholder="Describe your event (e.g. Wedding Reception, Corporate Summit, expected guest count, catering, AV equipment needs)..."><?php echo isset($description) ? $description :'' ?></textarea>
		</div>
	</form>
</div>
<script>
	 $('.datetimepicker').datetimepicker({
	      format:'Y/m/d H:i',
	      startDate: '+3d'
	  })
	$('#manage-book').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'admin/ajax.php?action=save_book',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("book Request Sent.",'success')
						end_load()
						uni_modal("","book_msg.php")

				}
			}
		})
	})
</script>