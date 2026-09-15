<?php 
include 'admin/db_connect.php'; 
?>
<style>
#portfolio .img-fluid{
    width: calc(100%);
    height: 30vh;
    z-index: -1;
    position: relative;
    padding: 1em;
}
.event-list{
cursor: pointer;
}
span.hightlight{
    background: yellow;
}
.banner{
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 26vh;
        width: calc(30%);
    }
    .banner img{
        width: calc(100%);
        height: calc(100%);
        cursor :pointer;
    }
.event-list{
cursor: pointer;
border: unset;
flex-direction: inherit;
}

.event-list .banner {
    width: calc(40%)
}
.event-list .card-body {
    width: calc(60%)
}
.event-list .banner img {
    border-top-left-radius: 5px;
    border-bottom-left-radius: 5px;
    min-height: 50vh;
}
span.hightlight{
    background: yellow;
}
.banner{
   min-height: calc(100%)
}
</style>
        <header class="masthead">
            <div class="container-fluid h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-8 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Welcome to <?php echo $_SESSION['system']['name']; ?></h3>
                        <hr class="divider my-4" />

                    <div class="col-md-12 mb-2 justify-content-center">
                    </div>                        
                    </div>
                    
                </div>
            </div>
        </header>
            <div class="container mt-3 pt-2">
                <h4 class="text-center text-white">Upcoming Events</h4>
                <hr class="divider">
                <?php
                $event = $conn->query("SELECT e.*,v.venue FROM events e inner join venue v on v.id=e.venue_id where date_format(e.schedule,'%Y-%m%-d') >= '".date('Y-m-d')."' and e.type = 1 order by unix_timestamp(e.schedule) asc");
                while($row = $event->fetch_assoc()):
                    $trans = get_html_translation_table(HTML_ENTITIES,ENT_QUOTES);
                    unset($trans["\""], $trans["<"], $trans[">"], $trans["<h2"]);
                    $desc = strtr(html_entity_decode($row['description']),$trans);
                    $desc=str_replace(array("<li>","</li>"), array("",","), $desc);
                ?>
                <div class="card event-list" data-id="<?php echo $row['id'] ?>">
                     <div class='banner'>
                        <?php if(!empty($row['banner'])): ?>
                            <img src="admin/assets/uploads/<?php echo($row['banner']) ?>" alt="">
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <div class="row  align-items-center justify-content-center text-center h-100">
                            <div class="">
                                <h3><b class="filter-txt"><?php echo ucwords($row['event']) ?></b></h3>
                                <div><small><p><b><i class="fa fa-calendar"></i> <?php echo date("F d, Y h:i A",strtotime($row['schedule'])) ?></b></p></small></div>
                                <hr>
                                <larger class="truncate filter-txt"><?php echo strip_tags($desc) ?></larger>
                                <br>
                                <hr class="divider"  style="max-width: calc(80%)">
                                <button class="btn btn-primary float-right read_more" data-id="<?php echo $row['id'] ?>">Read More</button>
                            </div>
                        </div>
                        

                    </div>
                </div>
                <br>
                <?php endwhile; ?>
            </div>

            <!-- User Reviews / Testimonials Section -->
            <div class="mt-5 pt-3">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <div>
                        <h4 class="text-white font-weight-bold mb-1">
                            <i class="fa fa-star text-warning mr-2"></i>What Our Clients Say
                        </h4>
                        <p class="text-white-50 mb-0">Verified experiences from guests and event organizers</p>
                    </div>
                    <button class="btn btn-primary mt-2 mt-sm-0" id="write_review_btn">
                        <i class="fa fa-pencil-alt mr-1"></i> Write a Review
                    </button>
                </div>
                <hr class="divider">

                <div class="row">
                    <?php
                    $revQry = $conn->query("SELECT * FROM reviews ORDER BY id DESC LIMIT 6");
                    if($revQry && $revQry->num_rows > 0):
                        while($rev = $revQry->fetch_assoc()):
                            $stars = str_repeat("★", $rev['rating']) . str_repeat("☆", 5 - $rev['rating']);
                    ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100 shadow-sm border-0" style="background: rgba(255,255,255,0.95); border-radius: 10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h5 class="card-title font-weight-bold mb-0 text-dark">
                                            <?php echo htmlspecialchars($rev['name']) ?>
                                        </h5>
                                        <?php if(!empty($rev['event_type'])): ?>
                                            <span class="badge badge-info mt-1"><?php echo htmlspecialchars($rev['event_type']) ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-warning font-weight-bold" style="font-size: 1.1rem; letter-spacing: 2px;">
                                        <?php echo $stars ?>
                                    </div>
                                </div>
                                <p class="card-text text-muted font-italic mb-2">
                                    "<?php echo nl2br(htmlspecialchars($rev['comment'])) ?>"
                                </p>
                                <div class="text-right small text-muted">
                                    <i class="fa fa-calendar-alt mr-1"></i><?php echo date("M d, Y", strtotime($rev['date_created'])) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php 
                        endwhile;
                    else:
                    ?>
                        <div class="col-12 text-center text-white-50 py-4">
                            <p>No reviews yet. Be the first to share your experience!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>


<script>
     $('#write_review_btn').click(function(){
         requireLogin(function(){
             uni_modal("Share Your Review & Experience", "submit_review.php");
         });
     });
     $('.read_more').click(function(){
         location.href = "index.php?page=view_event&id="+$(this).attr('data-id')
     })
     $('.banner img').click(function(){
        viewer_modal($(this).attr('src'))
    })
    $('#filter').keyup(function(e){
        var filter = $(this).val()

        $('.card.event-list .filter-txt').each(function(){
            var txto = $(this).html();
            txt = txto
            if((txt.toLowerCase()).includes((filter.toLowerCase())) == true){
                $(this).closest('.card').toggle(true)
            }else{
                $(this).closest('.card').toggle(false)
               
            }
        })
    })
</script>