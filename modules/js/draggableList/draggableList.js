function initDraggableList(params) {
    var items = typeof params.items == 'string' ? document.querySelectorAll(params.items) : params.items;
    var duration = 'duration' in params ? params.duration : 150;
    var afterDrag = params.afterDrag;

    for (var i=0; i<items.length; i++) {
        var handler = items[i].querySelector(params.handler);
        if (!handler) continue;
        if ('startDrag' in handler) {
            if (handler.draggableListIndex == i) continue;
            handler.removeEventListener('mousedown', handler.startDrag);
            handler.removeEventListener('touchstart', handler.startDrag);
        }
        handler.startDrag = startDrag.bind(null, i);
        handler.addEventListener('mousedown', handler.startDrag);
        handler.addEventListener('touchstart', handler.startDrag);
        handler.draggableListIndex = i;
    }


    function startDrag(i0, e) {
        if ('touches' in e) {
            isTouchEvent = true;
            e.clientY = e.touches[0].clientY
        } else {
            isTouchEvent = false;
        }

        e.preventDefault()
        e.stopPropagation()
        // init
        var
            s, // y-coordinades of separators between items
            y0,// start y-coordinate of mouse
            i1 // new index of dragged item

        items[i0].classList.add('on-drag')
        s = [0]
        for (var i=0; i<items.length; i++) {
            s.push(s[i] + items[i].offsetHeight)
        }
        y0 = e.clientY

        // drag event handler
        function onDrag(e) {
            if (isTouchEvent) e.clientY = e.touches[0].clientY 
            items[i0].style.top = (e.clientY - y0) + 'px'

            // get new index
            function getNearestIndex(x, a) {
                var r = -1
                for (var i=0; i<a.length; i++) if (r == -1 || Math.abs(a[r] - x) > Math.abs(a[i] - x)) r = i
                return r
            }
            var y = e.clientY - y0
            if (y < 0) {
                y += s[i0]
            } else {
                y += s[i0+1] 
            }
            i1 = getNearestIndex(y, s)

            // move elements away to get free space (this is only animation for UX)
            var h = s[i0+1] - s[i0]
            if (i1 == i0 || i1 == i0 + 1) {
                i1 = i0
                for (var i=0; i<items.length; i++) if (i != i1) {
                    items[i].style.top = '0px'
                }
            } else if (i1 < i0) {
                for (var i=0; i<items.length; i++) if (i != i0) {
                    if (i >= i1 && i < i0) {
                        items[i].style.top = h + 'px'
                    } else {
                        items[i].style.top = '0px'
                    }
                }
            } else {
                i1--
                for (var i=0; i<items.length; i++)  if (i != i0) {
                    if (i <= i1 && i > i0) {
                        items[i].style.top = -h + 'px'
                    } else {
                        items[i].style.top = '0px'
                    }
                }
            }
        }       

        function clearOffsets() {
            for (var i=0; i<items.length; i++) items[i].style.top = ''
        }         

        // drop handler
        function onDrop() {
            if (typeof i1 == 'undefined') i1 = i0
            var el = items[i0]
            if (i1 > i0) {
                el.style.top = (s[i1+1] - s[i0+1]) + 'px'
            } else {
                el.style.top = (s[i1] - s[i0]) + 'px'
            }
            el.classList.remove('on-drag')
            setTimeout(function() {
                clearOffsets();
                afterDrag(i0, i1, isTouchEvent); // here you should update data
            }, duration);
            if (isTouchEvent) {
                document.body.removeEventListener('touchmove', onDrag)
                document.body.removeEventListener('touchend', onDrop)
            } else {
                document.body.removeEventListener('mousemove', onDrag)
                document.body.removeEventListener('mouseup', onDrop)
            }
        }   

        // bind drag & drop handlers
        if (isTouchEvent) {
            document.body.addEventListener('touchmove', onDrag)
            document.body.addEventListener('touchend', onDrop)
        } else {
            document.body.addEventListener('mousemove', onDrag)
            document.body.addEventListener('mouseup', onDrop)
        }
    } 
}


      