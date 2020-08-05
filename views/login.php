<?php
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
?>
<div class="pt-5">
    <div class="block hero_banner bg-white">
        <div class="container">
            <div class="row">
                <div class="col-md-12 mb-4">
                    <h1>Login to proceed...</h1>
                </div>
                <div class="col-md-12">
                    <form action="<?= SUB_ROOT . ROUTE_LOGIN ?>" method="POST">
                        <div class="row">
                            <div class="col-md-12">
                                <input class="<?= $error ?>" name="username" type="text" placeholder="Username" />
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <input class="<?= $error ?>" name="password" type="password" placeholder="Password" />
                                <?php if (isset($_SESSION['errorMsg'])) { ?>
                                    <p class="text-center error"><?= $_SESSION['errorMsg']; ?></p>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="button">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- main -->