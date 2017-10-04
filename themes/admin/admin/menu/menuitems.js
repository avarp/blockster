window.addEventListener('load', function(){

    function httpRequest(method, uri, postData) {
        return new Promise(function(resolve, reject){
            var xhr = new XMLHttpRequest
            xhr.open(method, uri, true)
            xhr.onerror = function() {
                reject(new Error('Internet disconnected.'))
            }
            xhr.onload = function() {
                if (xhr.status == 200) {
                    resolve(xhr.responseText)
                } else {
                    reject(new Error(xhr.status+' '+xhr.statusText))
                }
            }
            xhr.send(postData)
        })
    }

})