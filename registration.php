<div class="container-fluid">
	<form action="" id="manage-register">
		<input type="hidden" name="id" value="<?php echo isset($id) ? $id :'' ?>">
		<input type="hidden" name="event_id" value="<?php echo isset($_GET['event_id']) ? $_GET['event_id'] :'' ?>">

		<!-- Google Sign-In Button — rendered by header.php when modal opens -->
		<div class="form-group text-center">
			<div id="google_signin_btn" style="display:flex; justify-content:center; min-height:44px;"></div>
			<div class="text-muted mt-2 mb-2"><small>— or fill in manually below —</small></div>
		</div>

		<div class="form-group">
			<label for="" class="control-label">Full Name</label>
			<input type="text" class="form-control" id="reg_name" name="name" value="<?php echo isset($name) ? $name :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Address</label>
			<textarea cols="30" rows="2" required="" name="address" class="form-control"><?php echo isset($address) ? $address :'' ?></textarea>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Email</label>
			<input type="email" class="form-control" id="reg_email" name="email" value="<?php echo isset($email) ? $email :'' ?>" required>
		</div>
		<div class="form-group">
			<label for="" class="control-label">Contact #</label>
			<input type="text" class="form-control" name="contact" value="<?php echo isset($contact) ? $contact :'' ?>" required>
		</div>
	</form>
</div>
<script>
	$('.datetimepicker').datetimepicker({
      format:'Y/m/d H:i',
      startDate: '+3d'
  })
	$('#manage-register').submit(function(e){
		e.preventDefault()
		start_load()
		$('#msg').html('')
		$.ajax({
			url:'admin/ajax.php?action=save_register',
			data: new FormData($(this)[0]),
		    cache: false,
		    contentType: false,
		    processData: false,
		    method: 'POST',
		    type: 'POST',
			success:function(resp){
				if(resp==1){
					alert_toast("Registration Request Sent.",'success')
						end_load()
						uni_modal("","register_msg.php")
				}
			}
		})
	})
</script>