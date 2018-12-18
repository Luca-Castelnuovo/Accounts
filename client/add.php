<?php

require($_SERVER['DOCUMENT_ROOT'] . '/includes/init.php');

loggedin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    client_create($_POST['CSRFtoken'], $_SESSION['id'], $_POST['logo_url'], $_POST['name'], $_POST['description'], $_POST['redirect_uri']);
}

page_header('Create Client');

?>

<style>
    .input-field input:focus + label {
        color: #2962FF !important;
    }

    .input-field input:focus {
        border-bottom: 1px solid #2962FF !important;
        box-shadow: 0 1px 0 0 #2962FF !important;
    }
</style>
<div class="row">
    <h4>Create Client</h4>
    <form method="post">
        <div class="row">
            <div class="input-field col s12">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" required/>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <label for="redirect_uri">Callback URL</label>
                <input type="text" id="redirect_uri" name="redirect_uri" required/>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <label for="logo_url">Logo URL</label>
                <input type="text" id="logo_url" name="logo_url"/>
            </div>
        </div>
        <div class="row">
            <div class="input-field col s12">
                <label for="description">Description</label>
                <textarea id="description" class="materialize-textarea" name="description"></textarea>
            </div>
        </div>

        <div class="row">
            <div class="col s12">
                <input type="hidden" name="CSRFtoken" value="<?= csrf_gen() ?>"/>
                <button class="col s12 btn-large waves-effect blue accent-4" type="submit">Create Client</button>
            </div>
        </div>
    </form>
</div>

<?= page_footer(); ?>
