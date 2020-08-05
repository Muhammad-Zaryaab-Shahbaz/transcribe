<?php
$statement = getStatement();
?>
<div class="container pb-5">
    <p class="description">
        <?= $statement ?>
    </p>
    <br />
    <button onclick="window.location.href='<?= ROUTE_RANDOM ?>'">suivant</button>
</div>
<script>
    setTimeout(() => window.location.href = '<?= ROUTE_RANDOM ?>', <?= REMINDER_SECONDS ?> * 1000)
</script>