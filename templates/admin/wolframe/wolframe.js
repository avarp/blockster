!function() {
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function nodeList2Array(nodeList) {
    var a = new Array()
    for (var i=0; i<nodeList.length; i++) a.push(nodeList[i])
    return a
}

function forceRedraw(element) {
    element.offsetWidth
    element.offsetHeight
}


function manageClass(element, cls, action) {
    var classes = element.className.split(' ')
    var i = classes.indexOf(cls)
    if (i == -1) { // class is not setted
        if (action == 'add') {
            classes.push(cls)
            element.className = classes.join(' ')
            return true
        } else {
            return false
        }
    } else { // class is setted
        if (action == 'remove') {
            classes.splice(i, 1)
            element.className = classes.join(' ')
            return true
        } else {
            return false
        }
    }
}


function animateHeight(element, duration, action) {
    if (element.offsetHeight > 0) {
        if (action == 'collapse') {
            element.style.height = getComputedStyle(element).height
            element.style.width = getComputedStyle(element).width
            element.style.overflow = 'hidden'
            element.style.transition = 
            'height '+duration+'ms ease-in-out,'+
            ' padding-top '+duration+'ms ease-in-out,'+
            ' padding-bottom '+duration+'ms ease-in-out,'+
            ' border-top-width '+duration+'ms ease-in-out,'+
            ' border-bottom-width '+duration+'ms ease-in-out'
            forceRedraw(element)
            element.style.height = '0px'
            element.style.paddingTop = '0px'
            element.style.paddingBottom = '0px'
            element.style.borderTopWidth = '0px'
            element.style.borderBottomWidth = '0px'
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
            }, duration)
            return true
        } else {
            return false
        }
    } else {
        if (action == 'expand') {
            var expandedDisplay = element.hasAttribute('data-wf-display') ? element.getAttribute('data-wf-display') : 'block'
            element.style.display = ''
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            var h = getComputedStyle(element).height
            var w = getComputedStyle(element).width
            var pt = getComputedStyle(element).paddingTop
            var pb = getComputedStyle(element).paddingBottom
            var btw = getComputedStyle(element).borderTopWidth
            var bbw = getComputedStyle(element).borderBottomWidth
            element.style.height = '0px'
            element.style.width = w
            element.style.paddingTop = '0px'
            element.style.paddingBottom = '0px'
            element.style.borderTopWidth = '0px'
            element.style.borderBottomWidth = '0px'
            element.style.transition = 'none'
            element.style.overflow = 'hidden'
            forceRedraw(element)
            element.style.transition = 
            'height '+duration+'ms ease-in-out,'+
            ' padding-top '+duration+'ms ease-in-out,'+
            ' padding-bottom '+duration+'ms ease-in-out,'+
            ' border-top-width '+duration+'ms ease-in-out,'+
            ' border-bottom-width '+duration+'ms ease-in-out'
            element.style.height = h
            element.style.paddingTop = pt
            element.style.paddingBottom = pb
            element.style.borderTopWidth = btw
            element.style.borderBottomWidth = bbw
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            }, duration)
            return true
        } else {
            return false
        }
    }
}


function animateWidth(element, duration, action) {
    if (element.offsetWidth > 0) {
        if (action == 'collapse') {
            element.style.height = getComputedStyle(element).height
            element.style.width = getComputedStyle(element).width
            element.style.overflow = 'hidden'
            element.style.transition = 
            'width '+duration+'ms ease-in-out,'+
            ' padding-left '+duration+'ms ease-in-out,'+
            ' padding-right '+duration+'ms ease-in-out,'+
            ' border-left-width '+duration+'ms ease-in-out,'+
            ' border-right-width '+duration+'ms ease-in-out'
            forceRedraw(element)
            element.style.width = '0px'
            element.style.paddingLeft = '0px'
            element.style.paddingRight = '0px'
            element.style.borderLeftWidth = '0px'
            element.style.borderRightWidth = '0px'
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
            }, duration)
            return true
        } else {
            return false
        }
    } else {
        if (action == 'expand') {
            var expandedDisplay = element.hasAttribute('data-wf-display') ? element.getAttribute('data-wf-display') : 'block'
            element.style.display = ''
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            var h = getComputedStyle(element).height
            var w = getComputedStyle(element).width
            var pl = getComputedStyle(element).paddingLeft
            var pr = getComputedStyle(element).paddingRight
            var blw = getComputedStyle(element).borderLeftWidth
            var brw = getComputedStyle(element).borderRightWidth
            element.style.height = h
            element.style.width = '0px'
            element.style.paddingLeft = '0px'
            element.style.paddingRight = '0px'
            element.style.borderLeftWidth = '0px'
            element.style.borderRightWidth = '0px'
            element.style.transition = 'none'
            element.style.overflow = 'hidden'
            forceRedraw(element)
            element.style.transition = 
            'width '+duration+'ms ease-in-out,'+
            ' padding-left '+duration+'ms ease-in-out,'+
            ' padding-right '+duration+'ms ease-in-out,'+
            ' border-left-width '+duration+'ms ease-in-out,'+
            ' border-right-width '+duration+'ms ease-in-out'
            element.style.width = w
            element.style.paddingLeft = pl
            element.style.paddingRight = pr
            element.style.borderLeftWidth = blw
            element.style.borderRightWidth = brw
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            }, duration)
            return true
        } else {
            return false
        }
    }
}


function animateModal(element, duration, modalInfo, action) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        if (action == 'hide') {
            if (typeof(element.onhide) == 'function') element.onhide(modalInfo)
            element.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
            forceRedraw(element)
            element.style.transform = 'scale(1.5)'
            element.style.opacity = '0'
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
            }, duration)
            return true
        } else {
            return false
        }
    } else {
        if (action == 'show') {
            var expandedDisplay = element.hasAttribute('data-wf-display') ? element.getAttribute('data-wf-display') : 'block'
            if (typeof(element.onshow) == 'function') element.onshow(modalInfo)
            element.style.display = ''
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            element.style.transform = 'scale(1.5)'
            element.style.opacity = '0'
            forceRedraw(element)
            element.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
            element.style.transform = 'scale(1)'
            element.style.opacity = '1'
            setTimeout(function(){
                element.style.cssText = ''
                if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
            }, duration)
            return true
        } else {
            return false
        }
    }
}


// event listeners
var listeners = {
    'toggleHeight': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                animateHeight(target, duration, 'collapse')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateHeight(group[i], duration, 'expand')
            } else {
                animateHeight(target, duration, 'expand')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateHeight(group[i], duration, 'collapse')
            }
        } else {
            if (!animateHeight(target, duration, 'expand')) animateHeight(target, duration, 'collapse')
        }
    },

    'toggleWidth': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                animateWidth(target, duration, 'collapse')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateWidth(group[i], duration, 'expand')
            } else {
                animateWidth(target, duration, 'expand')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateWidth(group[i], duration, 'collapse')
            }
        } else {
            if (!animateWidth(target, duration, 'expand')) animateWidth(target, duration, 'collapse')
        }
    },

    'toggleModal': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var inverse = ('inverse' in params)
        var modalInfo = ('modalInfo' in params) ? params.modalInfo : null
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                animateModal(target, duration, modalInfo, 'hide')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateModal(group[i], duration, modalInfo, 'show')
            } else {
                animateModal(target, duration, modalInfo, 'show')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) animateModal(group[i], duration, modalInfo, 'hide')
            }
        } else {
            if (!animateModal(target, duration, modalInfo, 'show')) animateModal(target, duration, modalInfo, 'hide')
        }
    },

    'toggleClass': function(params) {
        if ('class' in params) var cls = params['class']; else return;
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                manageClass(target, cls, 'remove')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) manageClass(group[i], cls, 'add')
            } else {
                manageClass(target, cls, 'add')
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) manageClass(group[i], cls, 'remove')
            }
        } else {
            if (!manageClass(target, cls, 'add')) manageClass(target, cls, 'remove')
        }
    }
}


window.addEventListener('load', function() {
    var activeElements = nodeList2Array(document.querySelectorAll('[data-wf-actions]'))
    activeElements.forEach(function(element){
        var tasklist = element.getAttribute('data-wf-actions')

        try {
            tasklist = JSON.parse(tasklist)
        } catch (e) {
            console.error('Parsing JSON error in attribute data-wf-actions of the element listed below: "'+e.name+'. '+e.message+'"')
            console.log(element)
            tasklist = null
        }

        if (tasklist) for (event in tasklist) {
            actions = tasklist[event]
            for (i=0, l=actions.length; i<l; i++) {
                a = actions[i]
                element.addEventListener(event, listeners[a.action].bind(element, a))
            }
        }
    })
    console.log('Wolframe v0.2 is started')
})
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}()