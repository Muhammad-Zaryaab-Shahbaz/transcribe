<style>
    .content-wrapper .text {
        padding-top: 100px;
        font-weight: 600;
    }

    .content-wrapper .text p {
        margin-right: 50px;
        padding-left: 45px;
    }

    .textarea {
        padding-left: 45px;
        height: auto;
        max-width: 90%;
    }

    textarea.error {
        border: 1px solid #be544a;
        box-shadow: 1px 1px 15px #be544a;
    }

    .error-text {
        color: #be544a;
    }

    .success-text {
        color: #265942;
    }
</style>
<div class="container-fluid content-wrapper">
    <div class="container text">
        <h3>RAPPEL</h3><br>
        <p>
            Merci de bien vouloir rappeler ci-dessous le plus précisément possible le texte que vous avez lu précédemment. Prenez le
            temps nécessaire puis cliquez sur “Suivant”.
        </p>
    </div>
    <br>
    <div class="container">
        <div class="form-group textarea">
            <textarea id="reminder" name="reminder" class="form-control" cols="55" rows="6"></textarea>
        </div>
        <span id="errorBlock" class="error-text pl-5 d-none">At least one sentence must be written.</span>
        <span id="successBlock" class="success-text pl-5 d-none">Thank you for writing at least one sentence.</span>
    </div>
    <br>

    <div class="container">
        <button id="submit" onclick="submitReminder()">suivant</button>
    </div>
</div>
<script>
    let timeSpent = 0;
    setInterval(function() {
        timeSpent++;
    }, 1000);
    
    $("#startClock").click(function() {
        var counter = 5;
        setInterval(function() {
            counter--;
            if (counter >= 0) {
                span = document.getElementById("count");
                span.innerHTML = counter;
            }
            if (counter === 0) {
                alert('sorry, out of time');
                clearInterval(counter);
            }
        }, 1000);
    });
    const checkError = (elem) => {
        const errorBlock = $("#errorBlock");
        const successBlock = $("#successBlock");
        if (elem.val() === '') {
            successBlock.removeClass("d-block");
            successBlock.addClass("d-none");

            errorBlock.removeClass("d-none");
            errorBlock.addClass("d-block");
            elem.addClass('error');
            return false;
        } else {
            errorBlock.removeClass("d-block");
            errorBlock.addClass("d-none");

            successBlock.removeClass("d-none");
            successBlock.addClass("d-block");
            elem.removeClass('error');
            return true;
        }
    }

    const submitReminder = () => {
        const elem = $("#reminder");
        if (!checkError(elem)) {
            return false;
        }

        $("#submit").css("display", "none");
        clearInterval(timeSpent);

        $.post('<?= ROUTE_SAVE_REMINDER ?>', {
                reminder: elem.val(),
                time: timeSpent
            })
            .done((url) => {
                if (url) {
                    // redirect to link
                    window.location.href = url;
                }
            })
        return false;
    }
</script>