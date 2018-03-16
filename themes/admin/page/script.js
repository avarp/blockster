function pushNotification(type, html) {
    var div = document.createElement('DIV');
    div.className = 'hidden mb-1 alert alert-'+type;
    div.innerHTML = '<button class="btn btn-'+type+' btn-sm btn-square alert-control"><i class="fa fa-times"></i></button>'+html;
    document.getElementById('notification-tray').appendChild(div);
    Mov.expandHeight({target:div});
    function remove(div) {
        Mov.collapseHeight({target:div}).then(function() {
            div.parentNode.removeChild(div);
        });
    }
    div.querySelector('.alert-control').addEventListener('click', remove.bind(null, div));
}