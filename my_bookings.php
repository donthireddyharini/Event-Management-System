<?php
if(!isset($_SESSION)) {
    session_start();
}
include 'admin/db_connect.php';

$isLoggedIn = isset($_SESSION['user_id']) || isset($_SESSION['user_email']);
$userEmail = isset($_SESSION['user_email']) ? $conn->real_escape_string($_SESSION['user_email']) : '';
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
?>
<header class="masthead" style="min-height: 35vh !important; height: 35vh !important;">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-8 align-self-end mb-4">
                <h2 class="text-white font-weight-bold">My Booking History</h2>
                <hr class="divider my-4" />
                <p class="text-white-75 mb-0">Track all your venue reservations and check their verification status</p>
            </div>
        </div>
    </div>
</header>

<div class="container my-5">
    <?php if(!$isLoggedIn): ?>
        <div class="card shadow-sm text-center p-5 mx-auto" style="max-width: 600px;">
            <div class="card-body">
                <i class="fa fa-lock fa-3x text-muted mb-3"></i>
                <h4 class="card-title font-weight-bold">Sign In to View Your Bookings</h4>
                <p class="text-muted mb-4">Please log in with your email or Google account to view your past venue reservations and booking requests.</p>
                <button class="btn btn-primary px-4 py-2 font-weight-bold" onclick="uni_modal('Sign In to Continue', 'user_auth.php')">
                    <i class="fa fa-sign-in-alt mr-1"></i> Sign In Now
                </button>
            </div>
        </div>
    <?php else: ?>
        <?php
        $where = [];
        if(!empty($userEmail)) $where[] = "b.email = '$userEmail'";
        if($userId > 0) $where[] = "b.user_id = $userId";
        $whereClause = !empty($where) ? implode(' OR ', $where) : "1=0";

        $qry = $conn->query("SELECT b.*, v.venue, v.address as venue_address, v.rate 
                             FROM venue_booking b 
                             LEFT JOIN venue v ON v.id = b.venue_id 
                             WHERE $whereClause 
                             ORDER BY b.id DESC");
        $totalBookings = $qry ? $qry->num_rows : 0;
        ?>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
            <div>
                <h4 class="font-weight-bold text-white mb-1">
                    <i class="fa fa-history mr-2 text-primary"></i>Your Reservations (<?php echo $totalBookings ?>)
                </h4>
                <p class="text-white-50 mb-0">Account: <?php echo htmlspecialchars($_SESSION['user_name']) ?> (<?php echo htmlspecialchars($_SESSION['user_email']) ?>)</p>
            </div>
            <a href="index.php?page=venue" class="btn btn-primary mt-2 mt-sm-0">
                <i class="fa fa-plus mr-1"></i> Book Another Venue
            </a>
        </div>

        <?php if($totalBookings == 0): ?>
            <div class="card shadow-sm text-center p-5">
                <div class="card-body">
                    <i class="fa fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="card-title font-weight-bold">No Booking Requests Found</h5>
                    <p class="text-muted mb-4">You haven't requested any venue bookings yet. Browse our selection of 8 premier event venues and book today!</p>
                    <a href="index.php?page=venue" class="btn btn-primary px-4 py-2 font-weight-bold">
                        <i class="fa fa-building mr-1"></i> Explore Event Venues
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row">
                <?php while($row = $qry->fetch_assoc()): 
                    $statusBadge = '<span class="badge badge-warning p-2"><i class="fa fa-clock mr-1"></i> For Verification</span>';
                    $borderClass = 'border-warning';
                    if($row['status'] == 1) {
                        $statusBadge = '<span class="badge badge-success p-2"><i class="fa fa-check-circle mr-1"></i> Confirmed</span>';
                        $borderClass = 'border-success';
                    } elseif($row['status'] == 2) {
                        $statusBadge = '<span class="badge badge-danger p-2"><i class="fa fa-times-circle mr-1"></i> Cancelled</span>';
                        $borderClass = 'border-danger';
                    }
                ?>
                <div class="col-lg-6 mb-4">
                    <div class="card shadow-sm h-100 border-left-lg <?php echo $borderClass ?>" style="border-left-width: 5px !important;">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="font-weight-bold mb-0 text-dark">
                                    <i class="fa fa-map-marker-alt text-danger mr-1"></i>
                                    <?php echo htmlspecialchars($row['venue'] ? $row['venue'] : 'Venue #' . $row['venue_id']) ?>
                                </h5>
                                <div><?php echo $statusBadge ?></div>
                            </div>
                            
                            <?php if(!empty($row['venue_address'])): ?>
                                <p class="text-muted small mb-3">
                                    <i class="fa fa-location-arrow mr-1"></i><?php echo htmlspecialchars($row['venue_address']) ?>
                                </p>
                            <?php endif; ?>

                            <div class="row text-muted small mb-3">
                                <div class="col-6 mb-2">
                                    <strong><i class="fa fa-calendar-alt text-primary mr-1"></i> Scheduled Date:</strong><br>
                                    <span class="text-dark font-weight-bold">
                                        <?php echo date("M d, Y h:i A", strtotime($row['datetime'])) ?>
                                    </span>
                                </div>
                                <div class="col-6 mb-2">
                                    <strong><i class="fa fa-hourglass-half text-primary mr-1"></i> Duration:</strong><br>
                                    <span class="text-dark font-weight-bold">
                                        <?php echo htmlspecialchars($row['duration']) ?>
                                    </span>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fa fa-user mr-1"></i> Contact Person:</strong><br>
                                    <?php echo htmlspecialchars($row['name']) ?>
                                </div>
                                <div class="col-6">
                                    <strong><i class="fa fa-phone mr-1"></i> Phone:</strong><br>
                                    <?php echo htmlspecialchars($row['contact']) ?>
                                </div>
                            </div>

                            <?php if(!empty($row['description'])): ?>
                                <div class="p-2 bg-light rounded small mb-2 border">
                                    <strong><i class="fa fa-info-circle text-info mr-1"></i> Event Requirements:</strong><br>
                                    <span class="text-dark"><?php echo nl2br(htmlspecialchars_decode($row['description'])) ?></span>
                                </div>
                            <?php endif; ?>

                            <div class="d-flex justify-content-between align-items-center small text-muted pt-2 border-top">
                                <span>Booking Ref: <strong>#VB-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></strong></span>
                                <?php if($row['rate']): ?>
                                    <span class="badge badge-light border">Rate: ₹<?php echo number_format($row['rate'], 2) ?>/hr</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>