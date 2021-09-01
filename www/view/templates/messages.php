<?php foreach(get_errors() as $error) : ?>
    <p class="font_err"><?=h($error)?></p>
<?php endforeach ?>

<?php foreach(get_messages() as $message) : ?>
    <p><?=h($message)?></p>
<?php endforeach ?>