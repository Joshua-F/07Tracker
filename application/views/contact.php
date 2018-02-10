<?php include 'themes/header.php'; ?>
            <!--<?=validation_errors('<div class="alert alert-error">', '</div>');?>

            <?=form_open('contact', array('class' => 'well span8'));?>

                <div class="row">
                    <div class="span3">
                        <?=form_label('First Name').form_input($formData['firstname']) . "\n";?>
                        <?=form_label('RuneScape Name').form_input($formData['runescapename']) . "\n";?>
                        <?=form_label('Email Address').form_input($formData['email']) . "\n";?>
                        <?=form_label('Subject').form_dropdown('subject', $formData['subject'], $this->input->post('subject') ? $this->input->post('subject') : 'na' , 'class="span3"') . "\n";?>
                    </div>
                    <div class="span5">
                        <?=form_label('Message').form_textarea($formData['message']) . "\n";?>
                    </div>
                    <button type="submit" class="btn btn-primary pull-right">Send</button>
                </div>
            <?=form_close();?>-->
            <div class="span5">
                <h2>Contact us</h2>
                <p>If you wish to contact us for any reason you must be registered on <a href="http://www.2007hq.com/forum">2007HQ.com</a> and send me(<a href="http://www.2007hq.com/forum/member.php?u=21">Joshua F</a>) a <a href="http://www.2007hq.com/forum/private.php?do=newpm&amp;u=21">Private Message.</a></p>
            </div>

            <div class="span6">
                <h2>Contact template</h2>
                <div class="well">
                    Contact reason:&nbsp;</br>
                    RuneScape(Current) Name:&nbsp;</br>
                    Message:&nbsp;
                </div>
            </div>

            <div class="clearfix"></div>
<?php include 'themes/footer.php'; ?>