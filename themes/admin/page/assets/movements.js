if (!('movements' in window)) window.movements = {}; (function() {
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

function forceRedraw(element)
{
    element.offsetWidth
    element.offsetHeight
}

function prepareParameters(p, list)
{
    if (typeof p != 'object') p = {}
    for (var i=0; i<list.length; i++) switch (list[i]) {
        case 'target':
        if (!('target' in p)) p.target = this
        if (typeof p.target == 'string') p.target = document.querySelector(p.target)
        break
        case 'source':
        if (!('source' in p)) p.source = this
        if (typeof p.source == 'string') p.source = document.querySelector(p.source)
        break
        case 'group':
        if (!('group' in p)) p.group = []
        if (typeof p.group == 'string') p.group = document.querySelectorAll(p.group)
        if (typeof p.target == 'number') p.target = p.group[p.target]
        break
        case 'duration':
        if (!('duration' in p)) p.duration = 300
        break
        case 'invert':
        p.invert = 'invert' in p && p.invert
        break
        case 'class':
        if (!('class' in p)) p['class'] = ''
        break
        case 'hidingClass':
        if (!('hidingClass' in p)) p.hidingClass = 'hidden'
        break
        case 'animation':
        if (!('animation' in p)) p.animation = 'default'
        break
    }
}

function addClass(target, cls)
{
    var classes = target.className.split(' ')
    var i = classes.indexOf(cls)
    if (i == -1) {
        classes.push(cls)
        target.className = classes.join(' ')
        return true
    } else {
        return false
    }
}

function removeClass(target, cls)
{
    var classes = target.className.split(' ')
    var i = classes.indexOf(cls)
    if (i != -1) {
        classes.splice(i, 1)
        target.className = classes.join(' ')
        return true
    } else {
        return false
    }
}

movements.addClass = function(p)
{
    prepareParameters.call(this, p, ['target', 'class'])
    return addClass(p.target, p['class'])
}

movements.removeClass = function(p)
{
    prepareParameters.call(this, p, ['target', 'class'])
    return removeClass(p.target, p['class'])
}

movements.toggleClass = function(p)
{
    prepareParameters.call(this, p, ['target', 'class', 'group', 'invert'])
    if (p.group.length > 0) {
        if (p.invert) {
            if (removeClass(p.target, p['class'])) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                addClass(p.group[i], p['class'])
            }
        } else {
            if (addClass(p.target, p['class'])) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                removeClass(p.group[i], p['class'])
            }
        }
    } else {
        if (!addClass(p.target, p['class'])) removeClass(p.target, p['class'])
    }
}

function expandHeight(target, duration, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetHeight > 0) {
        return false
    } else {
        removeClass(target, hidingClass)
        var computedStyle = getComputedStyle(target)
        var animateFrom = {
            height:'0', width:computedStyle.width, paddingTop:'0', paddingBottom:'0', marginTop:'0',
            marginBottom:'0', borderTopWidth:'0', borderBottomWidth:'0',
            transition:'none', overflow:'hidden'
        }
        var animateTo = {
            height:'', paddingTop:'', paddingBottom:'', marginTop:'',
            marginBottom:'', borderTopWidth:'', borderBottomWidth:'',
            transition:'all '+duration+'ms ease-in-out'
        }
        for (prop in animateTo) if (animateTo[prop] == '') animateTo[prop] = computedStyle[prop]
        for (prop in animateFrom) target.style[prop] = animateFrom[prop]
        forceRedraw(target)
        for (prop in animateTo) target.style[prop] = animateTo[prop]
        setTimeout(function(target){
            target.style.cssText = target.cssTextInitial
        }.bind(null, target), duration)
        return true
    }
}

collapseHeight = function(target, duration, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetHeight > 0) {
        var computedStyle = getComputedStyle(target)
        var animateFrom = {
            height:computedStyle.height, width:computedStyle.width, overflow:'hidden',
            transition:'all '+duration+'ms ease-in-out'
        }
        var animateTo = {
            height:'0', paddingTop:'0', paddingBottom:'0', marginTop:'0',
            marginBottom:'0', borderTopWidth:'0', borderBottomWidth:'0'
        }
        for (prop in animateFrom) target.style[prop] = animateFrom[prop]
        forceRedraw(target)
        for (prop in animateTo) target.style[prop] = animateTo[prop]
        setTimeout(function(target, hidingClass){
            addClass(target, hidingClass)
            target.style.cssText = target.cssTextInitial
        }.bind(null, target, hidingClass), duration)
        return true
    } else {
        return false;
    }
}

movements.expandHeight = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass'])
    return expandHeight(p.target, p.duration, p.hidingClass)
}

movements.collapseHeight = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass'])
    return collapseHeight(p.target, p.duration, p.hidingClass)
}

movements.toggleHeight = function(p) {
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass', 'group', 'invert'])
    if (p.group.length > 0) {
        if (p.invert) {
            if (collapseHeight(p.target, p.duration, p.hidingClass)) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                expandHeight(p.group[i], p.duration, p.hidingClass)
            }
        } else {
            if (expandHeight(p.target, p.duration, p.hidingClass)) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                collapseHeight(p.group[i], p.duration, p.hidingClass)
            }
        }
    } else {
        if (!expandHeight(p.target, p.duration, p.hidingClass)) collapseHeight(p.target, p.duration, p.hidingClass)
    }
}

function expandWidth(target, duration, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetWidth > 0) {
        return false
    } else {
        removeClass(target, hidingClass)
        var computedStyle = getComputedStyle(target)
        var animateFrom = {
            width:'0', height:computedStyle.height, paddingLeft:'0', paddingRight:'0', marginLeft:'0',
            marginRight:'0', borderLeftWidth:'0', borderRightWidth:'0',
            transition:'none', overflow:'hidden'
        }
        var animateTo = {
            width:'', paddingLeft:'', paddingRight:'', marginLeft:'',
            marginRight:'', borderLeftWidth:'', borderRightWidth:'',
            transition:'all '+duration+'ms ease-in-out'
        }
        for (prop in animateTo) if (animateTo[prop] == '') animateTo[prop] = computedStyle[prop]
        for (prop in animateFrom) target.style[prop] = animateFrom[prop]
        forceRedraw(target)
        for (prop in animateTo) target.style[prop] = animateTo[prop]
        setTimeout(function(target){
            target.style.cssText = target.cssTextInitial
        }.bind(null, target), duration)
        return true
    }
}

function collapseWidth(target, duration, hidingClass) {
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetWidth > 0) {
        var computedStyle = getComputedStyle(target)
        var animateFrom = {
            width:computedStyle.width, height:computedStyle.height, overflow:'hidden',
            transition:'all '+duration+'ms ease-in-out'
        }
        var animateTo = {
            width:'0', paddingLeft:'0', paddingRight:'0', marginLeft:'0',
            marginRight:'0', borderLeftWidth:'0', borderRightWidth:'0'
        }
        for (prop in animateFrom) target.style[prop] = animateFrom[prop]
        forceRedraw(target)
        for (prop in animateTo) target.style[prop] = animateTo[prop]
        setTimeout(function(target, hidingClass){
            addClass(target, hidingClass)
            target.style.cssText = target.cssTextInitial
        }.bind(null, target, hidingClass), duration)
        return true
    } else {
        return false;
    }
}

movements.expandWidth = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass'])
    return expandWidth(p.target, p.duration, p.hidingClass)
}

movements.collapseWidth = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass'])
    return collapseWidth(p.target, p.duration, p.hidingClass)
}

movements.toggleWidth = function(p) {
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass', 'group', 'invert'])
    if (p.group.length > 0) {
        if (p.invert) {
            if (collapseWidth(p.target, p.duration, p.hidingClass)) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                expandWidth(p.group[i], p.duration, p.hidingClass)
            }
        } else {
            if (expandWidth(p.target, p.duration, p.hidingClass)) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
                collapseWidth(p.group[i], p.duration, p.hidingClass)
            }
        }
    } else {
        if (!expandWidth(p.target, p.duration, p.hidingClass)) collapseWidth(p.target, p.duration, p.hidingClass)
    }
}

function showModal(target, duration, animation, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetWidth > 0 || target.offsetHeight > 0) {
        return false
    } else {
        removeClass(target, hidingClass)
        switch (animation) {
            case 'from-top':
            target.style.transform = 'translateY(-50vh)'
            break
            case 'from-bottom':
            target.style.transform = 'translateY(50vh)'
            break
            case 'from-right':
            target.style.transform = 'translateX(-50vh)'
            break
            case 'from-left':
            target.style.transform = 'translateX(50vh)'
            break
            case 'fall':
            target.style.transform = 'scale(1.5)'
            break
            case 'rise':
            target.style.transform = 'scale(0.66)'
            break
        }
        target.style.opacity = '0'
        forceRedraw(target)
        target.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
        target.style.transform = ''
        target.style.opacity = ''
        setTimeout(function(target){
            target.style.cssText = target.cssTextInitial
        }.bind(null, target), duration)
        return true
    }
}

function hideModal(target, duration, animation, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetWidth > 0 || target.offsetHeight > 0) {
        target.style.transition = 'transform '+duration+'ms ease-in-out, opacity '+duration+'ms ease-in-out'
        forceRedraw(target)
        switch (animation) {
            case 'from-top':
            target.style.transform = 'translateY(50vh)'
            break
            case 'from-bottom':
            target.style.transform = 'translateY(-50vh)'
            break
            case 'from-right':
            target.style.transform = 'translateX(50vh)'
            break
            case 'from-left':
            target.style.transform = 'translateX(-50vh)'
            break
            case 'fall':
            target.style.transform = 'scale(0.66)'
            break
            case 'rise':
            target.style.transform = 'scale(1.5)'
            break
        }
        target.style.opacity = '0'
        setTimeout(function(target, hidingClass){
            addClass(target, hidingClass)
            target.style.cssText = target.cssTextInitial
        }.bind(null, target, hidingClass), duration)
        return true
    } else {
        return false
    }
}

movements.showModal = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'animation', 'hidingClass'])
    return showModal(p.target, p.duration, p.animation, p.hidingClass)
}

movements.hideModal = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'animation', 'hidingClass'])
    return hideModal(p.target, p.duration, p.animation, p.hidingClass)
}

movements.toggleModal = function(p)
{
    prepareParameters.call(this, p, ['target', 'duration', 'hidingClass', 'group', 'animation'])
    if (p.group.length > 0) {
        if (showModal(p.target, p.duration, p.animation, p.hidingClass)) for (i=0, l=p.group.length; i<l; i++) if (p.group[i] != p.target) {
            hideModal(p.group[i], p.duration, p.animation, p.hidingClass)
        }
    } else {
        if (!showModal(p.target, p.duration, p.animation, p.hidingClass)) hideModal(p.target, p.duration, p.animation, p.hidingClass)
    }
}

function updateDropdownPos(target, source)
{
    var sRect = source.getBoundingClientRect()
    var tRect = target.getBoundingClientRect()
    target.style.left = sRect.left+'px'
    if (sRect.bottom + tRect.height > window.innerHeight) {
        target.style.top = (sRect.top - tRect.height)+'px'
        target.style.transformOrigin = 'left bottom'
    } else {
        target.style.top = sRect.bottom+'px'
        target.style.transformOrigin = 'left top'
    }
}

var updateCurrentDropdownPos = null, hideCurrentDropdown = null

function showDropdown(target, source, duration, hidingClass)
{
    if (!('cssTextInitial' in target)) target.cssTextInitial = target.style.cssText
    if (target.offsetWidth > 0 || target.offsetHeight > 0) {
        return false
    } else {
        removeClass(target, hidingClass)
        if (getComputedStyle(target).position != 'fixed') target.style.setProperty('position', 'fixed', 'important')
        target.style.zIndex = '1000';
        updateDropdownPos(target, source);    
        target.style.transform = 'scale(0)'
        forceRedraw(target)
        target.style.transition = 'transform '+duration+'ms ease-in-out'
        target.style.transform = 'scale(1)'

        updateCurentDropdownPos = updateDropdownPos.bind(null, target, source)
        if (typeof hideCurrentDropdown == 'function') hideCurrentDropdown()
        hideCurrentDropdown = hideDropdown.bind(null, target, source, duration, hidingClass)
        window.addEventListener('resize', updateCurentDropdownPos)
        window.addEventListener('scroll', updateCurentDropdownPos, true)
        document.body.addEventListener('click', hideCurrentDropdown, true)

        return true
    }
}

function hideDropdown(target, source, duration, hidingClass)
{
    if (typeof(hideCurrentDropdown) == 'function') {
        window.removeEventListener('resize', updateCurentDropdownPos)
        window.removeEventListener('scroll', updateCurentDropdownPos, true)
        document.body.removeEventListener('click', hideCurrentDropdown, true)
        hideCurrentDropdown = null
        updateCurentDropdownPos = null
    }

    if (target.offsetWidth > 0 || target.offsetHeight > 0) {
        target.style.transition = 'opacity '+duration+'ms ease-in-out'
        forceRedraw(target)
        target.style.opacity = '0'
        setTimeout(function(target, hidingClass){
            target.style.cssText = target.cssTextInitial
            addClass(target, hidingClass)
        }.bind(null, target, hidingClass), duration)
    }
}

movements.showDropdown = function(p)
{
    if (!('target' in p)) p.target = this.firstElementChild
    prepareParameters.call(this, p, ['target', 'source', 'duration', 'hidingClass']) 
    showDropdown(p.target, p.source, p.duration, p.hidingClass)
}

function reserveMethod(element, task) {
    Object.defineProperty(movements, task.do, {
        get: function() {
            return undefined
        },
        set: function(element, task, externalFunction) {
            Object.defineProperty(movements, task.do, {
                value: externalFunction,
                writable: true,
                configurable: true,
                enumerable: true 
            })
            if (task.when == 'init') {
                movements[task.do].call(element, task.with)
            } else {
                element.addEventListener(task.when, movements[task.do].bind(element, task.with))
            }
        }.bind(null, element, task),
        configurable: true,
        enumerable: true
    })
}

function parseDataAttributes() {
    var activeElements = document.querySelectorAll('[data-movements]')
    for (n=0; n<activeElements.length; n++) {
        element = activeElements[n]
        tasklist = element.getAttribute('data-movements')
        element.setAttribute('data-movements-parsed', tasklist)
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
            for (var i=0; i<tasklist.length; i++) {
                var task = tasklist[i]
                if (!('when' in  task) || !('do' in  task)) {
                    console.error('Wrong format of "data-movements" attribute of the element listed below.')
                    console.log(element)
                    continue
                }
                if (!('with' in task)) task.with = {}
                if (typeof movements[task.do] == 'undefined') {
                    console.error('Attempt to call method "movements.'+task.do+'" which is not defined.')
                    reserveMethod(element, task)
                } else {
                    if (task.when == 'init') {
                        movements[task.do].call(element, task.with)
                    } else {
                        element.addEventListener(task.when, movements[task.do].bind(element, task.with))
                    }
                }
            }
        }
    }
    if (activeElements.length > 0) console.log('Movements: parsed '+activeElements.length+' new "data-movements" attributes.')
}

window.addEventListener('load', parseDataAttributes)
if ('MutationObserver' in window) {
    var observer = new MutationObserver(parseDataAttributes)
    observer.observe(
        document.body,
        {"childList":true, "subtree":true}
    )
}

console.log('The "Movements" v0.11 is started')
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
})()