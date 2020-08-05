<style>
    .content-wrapper .text {
        padding-top: 70px;
        font-weight: 600;
    }

    .content-wrapper .text p {
        margin-right: 50px;
        padding-left: 45px;
    }

    .capitals .input-area {
        padding-left: 80px;
    }

    input[type='radio'] {
        width: 60px !important;
        margin: 0 0 0 70px;
    }

    input[type='text'] {
        padding: 2px 14px 3px 14px;
    }
</style>
<div class="container-fluid content-wrapper">
    <div class="container text">
        <h3>Les capitales</h3><br>
        <p>
            Voici une liste de cinq pays. Merci d’indiquer la capitale pour chacun d’entre eux. Si vous ne savez pas ou si vous avez
            un doute, vous pouvez utiliser la case correspondante.
        </p>
    </div>
    <br><br>
    <?php
    $questions = array("de l’Italie", "de la Belgique", "du Brésil", "de l’Australie", "de l’Allemagne");
    foreach ($questions as $question) { ?>
    <div class="container capitals">
        <div class="input-area">
            La capitale <?= $question ?> est :
            <div class="row">
                <div class="col-md-6">
                    <input type="text">
                </div>
                <div class="d-flex align-items-center">
                    <input id="radio" type="radio">
                    <p class="m-0">Je ne sais pass</p>
                </div>
            </div>
        </div>
    </div>
    <br><br>
    <?php } ?>
    <div class="container pb-4">
        <button onclick="window.location.href='<?= ROUTE_REMINDER ?>'">suivant</button>
    </div>
</div>
