<div class="container-fluid content-wrapper pt-5">
    <div class="container text">
        <h3>ADMIN</h3><br>

        <div class="row">
            <div class="col-md-4">
                <p>Nombre de participants :</p>
            </div>
            <div class="col-md-2">
                <p><?= fetchTotalParticipants() ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <p>Nombre de chaînes commencées :</p>
            </div>
            <div class="col-md-2">
                <p><?= fetchTotalChains() ?></p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <p>Nombre de chaînes terminées :</p>
            </div>
            <div class="col-md-4">
                <p><?= fetchTotalCompletedChains() ?> soit <?= fetchTotalUsersCompletedChains() ?> participants</p>
            </div>
        </div>
        <br>
        <div class="row align-items-center col-sm-6 col-xs-6">
            <p>Télécharger les données (csv, utf8)</p>
        </div>
    </div>
    <br>
    <div class="container mb-3">
        <button onclick="window.open('<?= ROUTE_EXPORT ?>','_blank')">TÉLÉCHARGER</button>
    </div>
</div>