<?php include 'themes/header.php'; ?>
            <div class="row">
                <div class="span12">
                    <h2>New User</h2>
                    <p>It seems the account you are looking for does not exist in our database. <?=anchor('updater/add/' . str_replace(array(' '), '+', $curUser), 'Click here');?> to add the user to the database and begin tracking.</p>
                </div>
            </div>
<?php include 'themes/footer.php'; ?>