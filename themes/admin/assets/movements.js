var movements = {}; (function() {
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function forceRedraw(element) {
    element.offsetWidth
    element.offsetHeight
}

movements.addClass = function(element, cls) {
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
movements.removeClass = function(element, cls) {
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

movements.expandHeight = function(element, duration) {
    if (element.offsetHeight > 0) {
        return false;
    } else {
        var cssTextInitial = element.style.cssText
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
        element.style.display = ''
        if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        var h = getComputedStyle(element).height
        var w = getComputedStyle(element).width
        var pt = getComputedStyle(element).paddingTop
        var pb = getComputedStyle(element).paddingBottom
        var mt = getComputedStyle(element).marginTop
        var mb = getComputedStyle(element).marginBottom
        var btw = getComputedStyle(element).borderTopWidth
        var bbw = getComputedStyle(element).borderBottomWidth
        element.style.height = '0px'
        element.style.width = w
        element.style.paddingTop = '0px'
        element.style.paddingBottom = '0px'
        element.style.marginTop = '0px'
        element.style.marginBottom = '0px'
        element.style.borderTopWidth = '0px'
        element.style.borderBottomWidth = '0px'
        element.style.transition = 'none'
        element.style.overflow = 'hidden'
        forceRedraw(element)
        element.style.transition = 
        'height '+duration+'ms ease-in-out,'+
        ' padding-top '+duration+'ms ease-in-out,'+
        ' padding-bottom '+duration+'ms ease-in-out,'+
        ' margin-top '+duration+'ms ease-in-out,'+
        ' margin-bottom '+duration+'ms ease-in-out,'+
        ' border-top-width '+duration+'ms ease-in-out,'+
        ' border-bottom-width '+duration+'ms ease-in-out'
        element.style.height = h
        element.style.paddingTop = pt
        element.style.paddingBottom = pb
        element.style.marginTop = mt
        element.style.marginBottom = mb
        element.style.borderTopWidth = btw
        element.style.borderBottomWidth = bbw
        setTimeout(function(){
            element.style.cssText = cssTextInitial
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        }, duration)
        return true
    }
}
movements.collapseHeight = function(element, duration) {
    if (element.offsetHeight > 0) {
        var cssTextInitial = element.style.cssText
        element.style.height = getComputedStyle(element).height
        element.style.width = getComputedStyle(element).width
        element.style.overflow = 'hidden'
        element.style.transition = 
        'height '+duration+'ms ease-in-out,'+
        ' padding-top '+duration+'ms ease-in-out,'+
        ' padding-bottom '+duration+'ms ease-in-out,'+
        ' margin-top '+duration+'ms ease-in-out,'+
        ' margin-bottom '+duration+'ms ease-in-out,'+
        ' border-top-width '+duration+'ms ease-in-out,'+
        ' border-bottom-width '+duration+'ms ease-in-out'
        forceRedraw(element)
        element.style.height = '0px'
        element.style.paddingTop = '0px'
        element.style.paddingBottom = '0px'
        element.style.marginTop = '0px'
        element.style.marginBottom = '0px'
        element.style.borderTopWidth = '0px'
        element.style.borderBottomWidth = '0px'
        setTimeout(function(){
            element.style.cssText = cssTextInitial
            if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
        }, duration)
        return true
    } else {
        return false;
    }
}

movements.expandWidth = function(element, duration) {
    if (element.offsetWidth > 0) {
        return false
    } else {
        var cssTextInitial = element.style.cssText
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
            element.style.cssText = cssTextInitial
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        }, duration)
        return true
    }
}
movements.collapseWidth = function(element, duration) {
    if (element.offsetWidth > 0) {
        var cssTextInitial = element.style.cssText
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
            element.style.cssText = cssTextInitial
            if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
        }, duration)
        return true
    } else {
        return false
    }
}

movements.showModal = function(element, duration, animation) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        return false
    } else {
        var cssTextInitial = element.style.cssText
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
        element.style.display = ''
        if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        switch (animation) {
            case 'from-top':
            element.style.transform = 'translateY(-50vh)'
            break
            case 'from-bottom':
            element.style.transform = 'translateY(50vh)'
            break
            case 'from-right':
            element.style.transform = 'translateX(-50vh)'
            break
            case 'from-left':
            element.style.transform = 'translateX(50vh)'
            break
            case 'fall':
            element.style.transform = 'scale(1.5)'
            break
            case 'rise':
            element.style.transform = 'scale(0.66)'
            break
        }
        element.style.opacity = '0'
        forceRedraw(element)
        element.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
        element.style.transform = ''
        element.style.opacity = ''
        setTimeout(function(){
            element.style.cssText = cssTextInitial
            if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        }, duration)
        return true
    }
}
movements.hideModal = function(element, duration, animation) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        var cssTextInitial = element.style.cssText
        element.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
        forceRedraw(element)
        switch (animation) {
            case 'from-top':
            element.style.transform = 'translateY(50vh)'
            break
            case 'from-bottom':
            element.style.transform = 'translateY(-50vh)'
            break
            case 'from-right':
            element.style.transform = 'translateX(50vh)'
            break
            case 'from-left':
            element.style.transform = 'translateX(-50vh)'
            break
            case 'fall':
            element.style.transform = 'scale(0.66)'
            break
            case 'rise':
            element.style.transform = 'scale(1.5)'
            break
        }
        element.style.opacity = '0'
        setTimeout(function(){
            element.style.cssText = cssTextInitial
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

movements.showDropdown = function(element, source, duration) {
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        return false
    } else {
        element.cssTextInitial = element.style.cssText
        var expandedDisplay = element.hasAttribute('data-expanded-display') ? element.getAttribute('data-expanded-display') : 'block'
        element.style.display = ''
        if (getComputedStyle(element).display == 'none') element.style.setProperty('display', expandedDisplay, 'important')
        if (getComputedStyle(element).position != 'fixed') element.style.setProperty('position', 'fixed', 'important')
        element.style.zIndex = '1000';
        updateDropdownPos(element, source);    
        element.style.transform = 'scale(0)'
        forceRedraw(element)
        element.style.transition = 'transform '+duration+'ms ease-in-out'
        element.style.transform = 'scale(1)'
        movements.updateCurentDropdownPos = updateDropdownPos.bind(null, element, source)
        if (typeof movements.hideCurrentDropdown == 'function') movements.hideCurrentDropdown()
        movements.hideCurrentDropdown = movements.hideDropdown.bind(null, element, source, duration)
        window.addEventListener('resize', movements.updateCurentDropdownPos)
        window.addEventListener('scroll', movements.updateCurentDropdownPos, true)
        document.body.addEventListener('click', movements.hideCurrentDropdown, true)
        return true
    }
}
movements.hideDropdown = function(element, source, duration) {
    if (typeof(movements.hideCurrentDropdown) == 'function') {
        document.body.removeEventListener('click', movements.hideCurrentDropdown)
        window.removeEventListener('resize', movements.updateCurentDropdownPos)
        window.removeEventListener('scroll', movements.updateCurentDropdownPos)
        movements.hideCurrentDropdown = undefined
        movements.updateCurentDropdownPos = undefined
    }
    if (element.offsetWidth > 0 || element.offsetHeight > 0) {
        element.style.transition = 'transform '+duration+'ms ease-in-out'
        forceRedraw(element)
        element.style.transform = 'scale(0)'
        setTimeout(function(){
            element.style.cssText = element.cssTextInitial
            if (getComputedStyle(element).display != 'none') element.style.setProperty('display', 'none', 'important')
        }, duration)
        return true
    } else {
        return false
    }
}

// event listeners
movements.listeners = {
    toggleHeight: function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var logic = ('logic' in params) ? params.logic : 'direct'  
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (logic == 'inverse') {
                movements.collapseHeight(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.expandHeight(group[i], duration)
            } else {
                movements.expandHeight(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.collapseHeight(group[i], duration)
            }
        } else {
            if (!movements.expandHeight(target, duration)) movements.collapseHeight(target, duration)
        }
    },

    toggleWidth: function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var logic = ('logic' in params) ? params.logic : 'direct'  
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (logic == 'inverse') {
                movements.collapseWidth(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.expandWidth(group[i], duration)
            } else {
                movements.expandWidth(target, duration)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.collapseWidth(group[i], duration)
            }
        } else {
            if (!movements.expandWidth(target, duration)) movements.collapseWidth(target, duration)
        }
    },

    toggleModal: function(params) {
        var duration = ('duration' in params) ? params.duration : 300
        var animation = ('animation' in params) ? params.animation : 'from-top'
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.hideModal(group[i], duration, animation)
            setTimeout(function() {
                movements.showModal(target, duration, animation)
            }, duration);
        } else {
            if (!movements.showModal(target, duration, animation)) movements.hideModal(target, duration, animation)
        }
    },

    toggleClass: function(params) {
        if ('class' in params) var cls = params['class']; else return;
        var logic = ('logic' in params) ? params.logic : 'direct'  
        var group = ('group' in params) ? document.querySelectorAll(params.group) : []
        var target
        if ('target' in params) {
            if (typeof(params.target) == 'string') target = document.querySelector(params.target)
            else target = group[params.target]
        } else {
            target = this;
        }
        if (group.length > 0) {
            if (logic == 'inverse') {
                movements.removeClass(target, cls)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.addClass(group[i], cls)
            } else {
                movements.addClass(target, cls)
                for (i=0, l=group.length; i<l; i++) if (group[i] != target) movements.removeClass(group[i], cls)
            }
        } else {
            if (!movements.addClass(target, cls)) movements.removeClass(target, cls)
        }
    },

    showDropdown: function(params) {
        var source = ('source' in params) ? document.querySelector(params.source) : this
        var target = ('target' in params) ? document.querySelector(params.target) : this.firstElementChild
        var initParams = ('initParams' in params) ? params.initParams : null
        var duration = ('duration' in params) ? params.duration : 150
        if (!movements.showDropdown(target, source, duration, initParams)) movements.hideDropdown(target, source, duration)
    }
}

movements.init = function() {
    var activeElements = document.querySelectorAll('[data-movements]')

    for (n=0; n<activeElements.length; n++) {
        element = activeElements[n]

        tasklist = element.getAttribute('data-movements')
        element.setAttribute('data-movements', tasklist)
        element.removeAttribute('data-movements')

        try {
            tasklist = JSON.parse(tasklist)
        } catch (e) {
            console.error('Parsing JSON error in attribute "data-movements" of the element listed below: "'+e.name+'. '+e.message+'"')
            console.log(element)
            tasklist = null
        }

        if (tasklist) {
            if (!Array.isArray(tasklist)) tasklist = [tasklist]

            for (let i=0; i<tasklist.length; i++) {
                let task = tasklist[i]
                if (!('when' in  task) || !('do' in  task)) {
                    console.error('Wrong format of "data-movements" attribute of the element listed below.')
                    console.log(element)
                    continue
                }
                if (!('with' in task)) task.with = {}
                if (typeof movements.listeners[task.do] != 'function') {
                    console.error('Unknown action "'+task.do+'" in attribute "data-movements" of the element listed below.')
                    console.log(element)
                    continue
                }
                element.addEventListener(task.when, movements.listeners[task.do].bind(element, task.with))
            }
        }
    }

    console.log('Movements: parsed '+activeElements.length+' new "data-movements" attributes.')
}


window.addEventListener('load', movements.init)

var observer = new MutationObserver(movements.init)
observer.observe(
    document.body,
    {"childList":true, "subtree":true}
)

console.log('The "Movements" v0.5.1 is started')
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
})()