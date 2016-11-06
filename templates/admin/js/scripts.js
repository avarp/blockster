window.onload = function() {

    // toggle height
    var heightTogglers = document.querySelectorAll('[data-toggle-height]');
    for (i=0; i<heightTogglers.length; i++) {
        heightTogglers[i].onclick = function() {
            if (this.hasAttribute('data-toggle-height')) {
                var target = document.getElementById(this.getAttribute('data-toggle-height'));
                if (target.offsetHeight > 0) {
                    target.style.height = getComputedStyle(target).height;
                    target.offsetWidth;
                    target.style.height = '0px';
                } else {
                    target.style.height = 'auto';
                    h = getComputedStyle(target).height;
                    target.style.height = '0px';
                    target.offsetWidth;
                    target.style.height = h;
                    setTimeout(function(){target.style.height = 'auto'}, 500);
                }
            }    
        }
    }

    // confirm submit
    var buttons = document.querySelectorAll('[data-confirm]');
    for (i=0; i<buttons.length; i++) {
        hiddenField = document.createElement('INPUT');
        hiddenField.type = 'hidden';
        hiddenField.name = buttons[i].name;
        hiddenField.value = buttons[i].value;
        buttons[i].form.appendChild(hiddenField);

        buttons[i].onclick = function() {
            var dialog = document.createElement('DIV');
            dialog.className = 'modal-wrap';
            dialog.style.display = 'block';
            dialog.style.opacity = '0';
            dialog.style.transform = 'scale(1.5)';
            dialog.innerHTML = 
            '<div class="modal card xs-11 sm-10 md-6 lg-4">\
                <div class="card-header"><i class="fa fa-lg fa-question-circle"></i></div>\
                <div class="card-section c">\
                    '+this.getAttribute('data-confirm')+'\
                </div>\
                <div class="card-section r">\
                    <button type="button" class="btn btn-flat">Да</button>\
                    <button type="button" class="btn btn-flat">Нет</button>\
                </div>\
            </div>';
            var yesBtn = dialog.getElementsByTagName('BUTTON')[0];
            var noBtn = dialog.getElementsByTagName('BUTTON')[1];
            yesBtn.target = this.form;

            yesBtn.onclick = function() {
                this.target.submit();
            }

            noBtn.onclick = function() {
                dialog.style.opacity = '0';
                dialog.style.transform = 'scale(1.5)';
                setTimeout(function(){document.body.removeChild(dialog)}, 250);
            }

            document.body.appendChild(dialog);
            dialog.offsetWidth;
            dialog.style.opacity = '1';
            dialog.style.transform = 'scale(1)';
            return false;
        }
    }

    // dialogs modal toggle
    var elements = document.querySelectorAll('[data-toggle-dialog]');
    for (i=0; i<elements.length; i++) {
        elements[i].onclick = function() {
            var id = this.getAttribute('data-toggle-dialog');
            var dialog = document.getElementById(id);
            if (dialog) {
                if (this.hasAttribute('data-dialog-init-param') && typeof dialog.init == 'function') {
                    dialog.init(this.getAttribute('data-dialog-init-param'));
                }
                if (dialog.offsetWidth == 0) {
                    dialog.style.opacity = '0';
                    dialog.style.transform = 'scale(1.5)';
                    dialog.style.display = 'block';
                    dialog.offsetWidth;
                    dialog.style.opacity = '1';
                    dialog.style.transform = 'scale(1)';
                } else {
                    dialog.style.opacity = '0';
                    dialog.style.transform = 'scale(1.5)';
                    setTimeout(function(){dialog.style.display = 'none'}, 250);
                }
            } else {
                console.log('Dialog #'+id+' not exists!');
            }
        }
    }


    // system messages
    var messages = document.querySelectorAll('.system-message');
    for (i=0; i<messages.length; i++) {
        messages[i].style.transform = 'scale(1)';
        messages[i].style.opacity = '1';
        messages[i].onmouseover = function() {
            this.style.opacity = '0';
            this.style.transform = 'scale(1.5)';
            var message = this;
            setTimeout(function(){message.parentNode.removeChild(message)}, 250);
        }
    }

    if (document.documentElement.offsetWidth < 768) document.getElementById('main-menu').style.height = '0px';
}

window.onresize = function() {
    if (document.documentElement.offsetWidth < 768) document.getElementById('main-menu').style.height = '0px';
    else document.getElementById('main-menu').style.height = 'auto';
}
