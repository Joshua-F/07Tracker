<?php include 'themes/header.php'; ?>
            <div class="row">
                <div class="span12">
                    <h3><?=!is_null( $skillid ) ? 'Daily ' .$skills[$skillid] . ' Top 50' : 'Top 50' ;?></h3>
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
                                <td><?=anchor('top/' . $count, '<img src="'.base_url().'assets/img/skill_icon_'.strtolower($skill).'1.gif"> '.$skill.'');?></td>
                            </tr>
<?php
$count++;
endforeach;
?>
                        </tbody>
                    </table>
                </div>
                <div class="span10">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Username</th>
                                <th>Experience Gained</th>
                            </tr>
                        </thead>
                        <tbody>
<?php
$count = 0;
foreach($users['data'] as $user):
?>
                            <tr>
                                <td><?=$count+1;?></td>
                                <td style="white-space: pre;"><?=anchor('track/' . str_replace(array(' '), '+', $this->index->getUsernameById($user['user_id'])), ucwords($this->index->getUsernameById($user['user_id'])));?></td>
                                <td><?=number_format($user[''.$users['skillid'].'']);?></td>
                            </tr>
<?php
$count++;
endforeach;
?>
                        </tbody>
                    </table>
                </div>
            </div>
<?php include 'themes/footer.php'; ?>