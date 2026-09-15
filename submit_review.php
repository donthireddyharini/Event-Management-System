<?php
if(!isset($_SESSION)) {
    session_start();
}
$rev_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
$rev_email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
?>
<div class="container-fluid p-2">
    <form id="submitReviewForm">
        <div class="form-group mb-2">
            <label class="font-weight-bold small mb-1">Your Name</label>
            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($rev_name) ?>" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold small mb-1">Email Address</label>
            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($rev_email) ?>" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold small mb-1">Event Type / Venue</label>
            <input type="text" class="form-control" name="event_type" placeholder="e.g. Wedding Reception, Corporate Summit, Birthday Party" required>
        </div>
        <div class="form-group mb-2">
            <label class="font-weight-bold small mb-1">Your Rating</label>
            <select class="form-control" name="rating" required>
                <option value="5">★★★★★ (5 Stars - Outstanding)</option>
                <option value="4">★★★★☆ (4 Stars - Very Good)</option>
                <option value="3">★★★☆☆ (3 Stars - Good)</option>
                <option value="2">★★☆☆☆ (2 Stars - Fair)</option>
                <option value="1">★☆☆☆☆ (1 Star - Poor)</option>
            </select>
        </div>
        <div class="form-group mb-3">
            <label class="font-weight-bold small mb-1">Your Review & Experience</label>
            <textarea class="form-control" name="comment" rows="3" placeholder="Tell us how we did! Describe your event experience, venue facilities, and staff service..." required></textarea>
        </div>
        <div id="review_alert" class="alert alert-danger py-1 px-2 small" style="display:none;"></div>
        <div class="d-flex justify-content-between align-items-center">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" style="background-color:#f4623a; border-color:#f4623a;">
                <i class="fa fa-paper-plane mr-1"></i> Submit Review
            </button>
        </div>
    </form>
</div>
<script>
$('#uni_modal .modal-footer').hide();
$('#submitReviewForm').submit(function(e){
    e.preventDefault();
    start_load();
    $.post('user_auth.php', $.extend({action:'save_review'}, Object.fromEntries(new FormData(this))), function(resp){
        end_load();
        try {
            var r = JSON.parse(resp);
            if(r.status == 1) {
                $('#uni_modal').modal('hide');
                if(typeof alert_toast === 'function') alert_toast(r.msg, 'success');
                setTimeout(function(){ location.reload(); }, 1200);
            } else {
                $('#review_alert').html(r.msg).fadeIn();
            }
        } catch(err) {
            $('#review_alert').html('Error submitting review').fadeIn();
        }
    });
});
</script>