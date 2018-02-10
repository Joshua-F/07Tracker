<?php include 'themes/header.php'; ?>
            <?php if (isset($userInfo['error'])): ?>
            <div class="alert alert-error">
                <?=$userInfo['error'];?>
            </div>
            <?php endif; ?>

            <div class="row">
                <div class="span10">
                    <h3 style="white-space: pre;"><?=$curUser;?>'s User Page<?=isset($history_date) ? " (".date('F j, Y', strtotime(str_replace("-", "/", $history_date))).")" : null ;?> <small>(Combat level: <?=$comatLevel;?>)</small></h3>
                    <?php if($this->uri->segment(3, "") != "history"): ?>
                    <p>
                        <?=anchor('updater/update/' . str_replace(array(' '), '+', $curUser), 'Update user stats', 'title="Update user stats"');?> (Last updated <?=timespan($userInfo['last_check'])?> ago)
                    </p>
                    <?php endif; ?>
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>Rank</th>
                                <th>Level</th>
                                <th>EXP</th>
                                <th><abbr title="Day Rank Difference">DRD</abbr></th>
                                <th><abbr title="Day Level Difference">DLD</abbr></th>
                                <th><abbr title="Day Experience Difference">DED</abbr></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $count = 0;
                            foreach($skills as $skill):
                            ?>
<tr>
                                <td style="text-align: center;"><img src="<?=base_url();?>assets/img/skill_icon_<?=strtolower($skill);?>1.gif"></td>
                                <td><?=number_format($userInfo['ranks'][$count]);?></td>
                                <td><?=$userInfo['xp'][$count] >= 14391160 && $skill != 'Overall' ? getVirturalLevel($userInfo['xp'][$count]) : number_format($userInfo['levels'][$count]);?></td>
                                <td><?=formatExperience($userInfo['levels'][$count], $userInfo['xp'][$count], $skill);?></td>
                                <td><?=colorize($userInfo['ranksDifferent'][$count], true);?></td>
                                <td><?=colorize($userInfo['levelsDifferent'][$count]);?></td>
                                <td><?=colorize($userInfo['xpDifferent'][$count]);?></td>
                            </tr>
                            <?php
                            $count++;
                            endforeach;
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="span2" style="margin-top: 97px;">
                   <!-- <?=anchor('track/' . $curUser . '/graphs', 'Graphs', 'class="btn"');?><br /><br /> -->
                    <form action="<?=site_url('track/' . $curUser . '/history');?>" method="post">
                        <div class="control-group">
                            <div class="controls">
                                <label>History date lookup</label>
                                <select name="date" class="selectpicker span2">
                                    <?php foreach ($history_dates as $date): ?>
<option<?=isset($history_date) && $date == strtotime(str_replace("-", "/", $history_date)) ? " selected=\"selected\"" : null ;?> value="<?=$date;?>"><?=date("M j, Y", $date);?></option>
                                    <?php endforeach; ?>
                                </select>
                                <button class="btn" type="submit">Lookup History</button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="span12">
                    <?php if($this->uri->segment(3, "") != "history"): ?>
                    <div id="graph1" style="min-width: 400px; height: 400px; margin: 0 auto"></div>
                    <?php endif; ?>
                    <p>
                        Note: Remember the lower the rank the better, that is why negative numbers are green when regarding rank.
                    </p>
                </div>
            </div>
<?php include 'themes/footer.php'; ?>