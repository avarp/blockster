function Wolframe() {
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var WF = this

function forceRedraw(element) {
    element.offsetWidth
    element.offsetHeight
}

WF.addClass = function(element, cls) {
    var classes = element.className.split(' ')
    var i = classes.indexOf(cls)
    if (i == -1) {
        classes.push(cls)
        element.className = classes.join(' ')
        return true
    } else {
        return false
    }
}
WF.removeClass = function(element, cls) {
    var classes = element.className.split(' ')
    var i = classes.indexOf(cls)
    if (i != -1) {
        classes.splice(i, 1)
        element.className = classes.join(' ')
        return true
    } else {
        return false
    }
}

WF.expandHeight = function(element, duration) {
    if (element.offsetHeight > 0) {
        return false;
    } else {
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
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
    }
}
WF.collapseHeight = function(element, duration) {
    if (element.offsetHeight > 0) {
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
        return false;
    }
}

WF.expandWidth = function(element, duration) {
    if (element.offsetWidth > 0) {
        return false
    } else {
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
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
    }
}
WF.collapseWidth = function(element, duration) {
    if (element.offsetWidth > 0) {
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
}

WF.showModal = function(element, duration, initParams) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        return false
    } else {
        if (typeof(element.onshow) == 'function' && element.onshow(initParams) === false) return false
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
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
    }
}
WF.hideModal = function(element, duration) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        if (typeof(element.onhide) == 'function' && element.onhide() === false) return false
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
}


function updateDropdownPos(element, source) {
    var src = source.getBoundingClientRect()
    var elm = element.getBoundingClientRect()
    element.style.left = src.left+'px'
    if (src.bottom + elm.height > window.innerHeight) {
        element.style.top = (src.top - elm.height)+'px'
        element.style.transformOrigin = 'left bottom'
    } else {
        element.style.top = src.bottom+'px'
        element.style.transformOrigin = 'left top'
    }
}

WF.showDropdown = function(element, source, duration, initParams) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        return false
    } else {
        if (typeof(element.onshow) == 'function' && element.onshow(initParams) === false) return false
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
        element.style.display = ''
        if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        if (getComputedStyle(element).position != 'fixed') element.style.setProperty('position', 'fixed', 'important')
        element.style.zIndex = '1000';
        forceRedraw(element);
        updateDropdownPos(element, source);    
        element.style.transform = 'scale(0)'
        forceRedraw(element)
        element.style.transition = 'transform '+duration+'ms ease-in-out'
        element.style.transform = 'scale(1)'
        setTimeout(function(){
            element.style.display = ''
            element.style.transform = ''
            element.style.transition = ''
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        }, duration)
        WF.updateCurentDropdownPos = updateDropdownPos.bind(null, element, source)
        WF.hideCurrentDropdown = WF.hideDropdown.bind(null, element, source, duration)
        window.addEventListener('resize', WF.updateCurentDropdownPos)
        window.addEventListener('scroll', WF.updateCurentDropdownPos, true)
        document.body.addEventListener('click', WF.hideCurrentDropdown, true)
        return true
    }
}
WF.hideDropdown = function(element, source, duration) {
    if (typeof(WF.hideCurrentDropdown) == 'function') {
        document.body.removeEventListener('click', WF.hideCurrentDropdown)
        window.removeEventListener('resize', WF.updateCurentDropdownPos)
        window.removeEventListener('scroll', WF.updateCurentDropdownPos)
        WF.hideCurrentDropdown = undefined
        WF.updateCurentDropdownPos = undefined
    }
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        if (typeof(element.onhide) == 'function' && element.onhide() === false) return false
        element.style.transition = 'transform '+duration+'ms ease-in-out'
        forceRedraw(element)
        element.style.transform = 'scale(0)'
        setTimeout(function(){
            element.style.cssText = ''
            if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
        }, duration)
        return true
    } else {
        return false
    }
}

// wolframe event listeners (handlers)
WF.listeners = {
    'toggleHeight': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                WF.collapseHeight(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.expandHeight(group[i], duration)
            } else {
                WF.expandHeight(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.collapseHeight(group[i], duration)
            }
        } else {
            if (!WF.expandHeight(target, duration)) WF.collapseHeight(target, duration)
        }
    },

    'toggleWidth': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                WF.collapseWidth(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.expandWidth(group[i], duration)
            } else {
                WF.expandWidth(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.collapseWidth(group[i], duration)
            }
        } else {
            if (!WF.expandWidth(target, duration)) WF.collapseWidth(target, duration)
        }
    },

    'toggleModal': function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var initParams = ('initParams' in params) ? params.initParams : null
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.hideModal(group[i], duration)
            setTimeout(function() {
                WF.showModal(target, duration, initParams)
            }, duration);
        } else {
            if (!WF.showModal(target, duration, initParams)) WF.hideModal(target, duration)
        }
    },

    'toggleClass': function(params) {
        if ('class' in params) var cls = params['class']; else return;
        var inverse = ('inverse' in params)
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (inverse) {
                WF.removeClass(target, cls)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.addClass(group[i], cls)
            } else {
                WF.addClass(target, cls)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) WF.removeClass(group[i], cls)
            }
        } else {
            if (!WF.addClass(target, cls)) WF.removeClass(target, cls)
        }
    },

    'showDropdown': function(params) {
        var source = ('source' in params) ? document.querySelector(params.source) : this
        var target = ('target' in params) ? document.querySelector(params.target) : this.firstElementChild
        var initParams = ('initParams' in params) ? params.initParams : null
        var duration = ('duration' in params) ? params.duration : 150
        if (!WF.showDropdown(target, source, duration, initParams)) WF.hideDropdown(target, source, duration)
    }
}

function nodeList2Array(nodeList) {
    var a = new Array()
    for (var i=0; i<nodeList.length; i++) a.push(nodeList[i])
    return a
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
                if (typeof WF.listeners[a.action] == 'function') {
                    element.addEventListener(event, WF.listeners[a.action].bind(element, a))
                } else {
                    console.error('Unknown action "'+a.action+'" in attribute data-wf-actions of the element listed below.')
                    console.log(element)
                }
            }
        }
    })
    console.log('Wolframe v0.3 is started');
})
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}
var wolframe = new Wolframe();