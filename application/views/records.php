<?php include 'themes/header.php'; ?>
            <div class="row">
                <div class="span12">
                    <h3><?=$title;?></h3>
                </div>
                <div class="span2">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
$count = 0;
foreach($skills as $skill):
?>
                            <tr>
                                <td><?=anchor('records/' . $type . '/' . $count, '<img src="'.base_url().'assets/img/skill_icon_'.strtolower($skill).'1.gif"> '.$skill.'');?></td>
                            </tr>
<?php
$count++;
endforeach;
?>
                        </tbody>
                    </table>
                </div>
                <div class="span8">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Username</th>
                                <th>Experience</th>
                                <th>Date</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i < count($userData['usernames']); $i++): ?>
                            <tr>
                                <td><?=$i+1;?></td>
                                <td style="white-space: pre;"><?=anchor('track/' . str_replace(array(' '), '+', $userData['usernames'][$i]), ucwords($userData['usernames'][$i]));?></td>
                                <td><?=number_format($userData['xp'][$i]);?></td>
                                <td><?=date('M j, Y', $userData['dates'][$i]);?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </div>
                <div class="span2">
                    <?=anchor('/records', 'Daily Records', 'class="btn btn-block"');?>
                    <!-- <?=anchor('/records/weekly', 'Weekly Records', 'class="btn btn-block"');?>
                    <?=anchor('/records/monthly', 'Monthly Records', 'class="btn btn-block"');?> -->
                </div>
            </div>
<?php include 'themes/footer.php'; ?>