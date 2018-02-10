<?php include 'themes/header.php'; ?>
            <h2>Signature Generation</h2>
            <div class="row">
                <div class="span5 offset4" style="width: 440px;">
                    <form id="form" class="well">
                        <label>Username</label>
                        <input type="text" name="username" value="<?=$curUser;?>" placeholder="Username" />
                        <label>Signature Design</label>
                        <select id="type" name="type" class="span3">
                            <option value="default" selected="selected">Default (Varrock)</option>
                            <option value="varrock_bank">Varrock Bank</option>
                            <option value="lumbridge">Lumbridge</option>
                            <option value="canifis">Canifis</option>
                            <option value="chaos_altar">Chaos Altar</option>
                            <option value="dark_warriors">Dark Warriors Fortress</option>
                            <option value="farming">Farming</option>
                            <option value="lesser_demons">Lesser Demons</option>
                            <option value="rellekka">Rellekka</option>
                            <option value="ruins">Ruins</option>
                            <option value="seers">Seers</option>
                            <option value="wildy">Wilderness Ditch</option>
                        </select>
                        <label>Example</label>
                        <img id="example" src="<?=site_url('sig/Vault.png');?>" />
                        <br style="display: block; margin-top: 7px;" />
                        <button type="submit" class="btn btn-primary">Generate</button>
                    </form>
                </div>
                <br style="display: block;" />
                <div id="links" class="span5 offset4 well" style="width: 400px; display: none;">
                    <label>Preview</label>
                    <img id="finishedExample" style="margin-bottom: 10px;" />
                    <label>Direct Link:</label>
                    <input id="direct" style="width: 97%;" type="text" value="" />
                    <label>BBCode:</label>
                    <input id="bbcode" style="width: 97%;" type="text" value="" />
                    <label>HTML:</label>
                    <input id="html" style="width: 97%;" type="text" value="" />
                </div>
            </div>
            <script type="text/javascript" src="<?=base_url('assets/js/siggen.js');?>"></script>
            <script type="text/javascript">
                baseUrl = '<?=base_url();?>';
                siteUrl = '<?=site_url();?>';
            </script>
<?php include 'themes/footer.php'; ?>