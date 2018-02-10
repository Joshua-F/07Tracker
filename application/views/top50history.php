<?php include 'themes/header.php'; ?>
            <div class="row">
                <div class="span12">
                    <h3><?=!is_null( $skillid ) ? $skills[$skillid] . ' Top 50 History for ' . date('F j, Y', $dateFlash) : 'Top 50 History' ;?></h3>
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
                                <td><?=anchor('top/history/' . $count, '<img src="'.base_url().'assets/img/skill_icon_'.strtolower($skill).'1.gif"> '.$skill.'');?></td>
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
                            </tr>
                        </thead>
                        <tbody>
<?php
$count = 1;
foreach($users as $user):
?>
                            <tr>
                                <td><?=$count;?></td>
                                <td style="white-space: pre;"><?=anchor('track/' . str_replace(array(' '), '+', $this->index->getUsernameById($user['user_id'])), ucwords($this->index->getUsernameById($user['user_id'])));?></td>
                                <td><?=number_format($user[''.$skillid.'']);?></td>
                            </tr>
<?php
$count++;
endforeach;
?>
                        </tbody>
                    </table>
                </div>
                <div class="span2">
                    <form action="<?php echo $this->uri->segment(3, null) === null ? site_url('/top/history') : site_url('/top/history/' . $this->uri->segment(3)) ; ?>" method="post">
                        <div class="control-group">
                            <div class="controls">
                                <label>History date lookup</label>
                                <select name="date" class="selectpicker span2">
<?php foreach ($dates as $date): ?>
                                    <option<?php echo $dateFlash == $date ? " selected=\"selected\"" : null ; ?> value="<?=$date;?>"><?=date('M j, Y', $date);?></option>
<?php endforeach; ?>
                                </select>
                                <button class="btn" type="submit">Lookup History</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
<?php include 'themes/footer.php'; ?>