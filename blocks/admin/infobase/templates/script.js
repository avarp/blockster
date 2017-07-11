!function() {
    var observer = new MutationObserver(function(){
        var blocks = document.querySelectorAll('[data-infobase-table]')

        for (var i=0; i<blocks.length; i++) {
            var table = blocks[i].getAttribute('data-infobase-table')
            var tableParams = JSON.parse(getTableParams(table))
            
            // обработчики нажатия кнопок верхней панели и сабмитов окон "Фильтр" и "Вид"
            // обработчик флажка "Выделить всё"
            // обработчики флажков выделения
            // обработчики пунктов контекстного меню для записей
            // обработчики кнопок страничной навигации
            // обработчики горячих клавиш на клавиатуре (del, ctrl+c, ctrl+x, ctrl+v)

        }
    })

    observer.observe(
        document.getElementById('tables'),
        {"childList":true, "subtree":true}
    )

    var blocks = document.querySelectorAll('[data-infobase-table]')
    for (var i=0; i<blocks.length; i++) {
        var table = blocks[i].getAttribute('data-infobase-table')
        var tableParams = getTableParams(table)

        var xhr = new XMLHttpRequest
        xhr.block = blocks[i]
        xhr.open('POST', SITE_URL+'/ajax/backend/infobase::actionShowTable?basename='+INFOBASE_NAME, true)
        xhr.onload = function() {this.block.innerHTML = this.responseText}
        var post = new FormData
        post.append('params', tableParams)
        xhr.send(post)
    }

    function getTableParams(table) {
        var tableParams = localStorage.getItem(INFOBASE_NAME+'-'+table)
        if (!tableParams) {
            tableParams = '{"table":"'+table+'"}'
            localStorage.setItem(INFOBASE_NAME+'-'+table, tableParams)
        }
        return tableParams
    }

    function getMyTable(element) {
        var x = element
        do {
            x = x.parentNode
        } while (x.hasAttribute('data-infobase-table') && x.tagName != 'BODY')
        if (x.hasAttribute('data-infobase-table')) {
            return x.getAttribute('data-infobase-table')
        } else {
            return false;
        }
    }

    function getMyId(element) {
        var x = element
        do {
            x = x.parentNode
        } while (x.hasAttribute('data-record-id') && x.tagName != 'BODY')
        if (x.hasAttribute('data-record-id')) {
            return x.getAttribute('data-record-id')
        } else {
            return false;
        }
    }

    function updateTable(table) {
        var xhr = new XMLHttpRequest()
        var table = document.querySelector('[data-infobase-table="'+table+'"]')

        xhr.open('POST', SITE_URL+'/ajax/backend/infobase::actionShowTable?basename='+INFOBASE_NAME, true)
        xhr.onload = function() {table.innerHTML = this.responseText}
        var post = new FormData;
        post.append('params', tableParams);
        xhr.send(post)
    }

    function deleteSelected() {
        
    }


}()