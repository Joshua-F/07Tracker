<?php include 'themes/header.php'; ?>
            <div class="alert alert-info">
                Want to start tracking your own account? Simply type your display name into the search box at the top right of this website.
            </div>
            <div class="hero-unit">
                <h2>Latest News</h2>
                <p>
                    Signatures are now here! <a href="http://www.07tracker.com/signature">Click here</a> to go and generate one!
                </p>
                <!-- <p><a href="#" class="btn btn-primary btn-large">Learn more &raquo;</a></p> -->
            </div>
            <div class="row">
                <?php
                $midnight = strtotime("tomorrow 00:00:00");
                $timeuntil = timespan(time(), $midnight);
                ?>
                <div class="span3">
                    <h2>What is this?</h2>
                    <p>2007 Tracker is a website designed to analyze and track yours and other players' levels.</p>
                </div>
                <div class="span6">
                    <h2>Time Until Update</h2>
                    <p><?=$timeuntil;?> until the next update.</p>
                    <p>This count down is until the global update for all users in the database; you can manually update by going to a users page.</p>
                </div>
                <div class="span3">
                    <h2>Stats</h2>
                    <p>
                        Total Users: <?=number_format($totalUsers);?><br />
                        Total XP Gained: <?=number_format($totalXPGained);?><br />
                        Total Levels Gained: <?=number_format($totalLevelsGained);?>
                    </p>
                </div>
            </div>
<?php include 'themes/footer.php'; ?>