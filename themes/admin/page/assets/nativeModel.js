var nativeModel = {}; !function()
{
    var partialClone = function(donor)
    {
        if (typeof donor != 'object') return donor
        if (Array.isArray(donor)) {
            var clone = []
            for (let i=0; i < donor.length; i++) {
                clone.push(donor[i])
            }    
        } else {
            var clone = Object.assign({}, donor)
        }
        return clone
    }

    function initializeAndBind(object, key, functions)
    {
        Object.defineProperty(object, 'observable_'+key, {
            value: {
                value: undefined,
                controllers: functions.controllers,
                observers: functions.observers
            },
            writable: true,
            configurable: true,
            enumerable: false,
        })

        var initialValue = object[key]

        Object.defineProperty(object, key, {
            get: function() {
                return partialClone(this['observable_'+key].value)
            }.bind(object),
            set: function(newval) {
                var observable = this['observable_'+key]
                var previousValue = partialClone(observable.value)
                for (let i=observable.controllers.length-1; i>=0; i--) {
                    newval = observable.controllers[i](previousValue, newval)
                }
                observable.value = newval
                for (let i=0; i<observable.observers.length; i++) {
                    observable.observers[i](previousValue, partialClone(observable.value))
                }
            }.bind(object),
            configurable: true,
            enumerable: true
        })

        object[key] = initialValue
    }

    function onlyBind(object, key, functions)
    {
        var observable = object['observable_'+key]
        for (let i=0; i<functions.observers.length; i++) observable.observers.push(functions.observers[i])
        for (let i=0; i<functions.controllers.length; i++) observable.controllers.push(functions.controllers[i])
        object[key] = observable.value
    }

    nativeModel.bind = function(object, key, functions)
    {
        if (typeof object != 'object' && typeof key != 'string') return
        if (typeof functions == 'undefined') return

        if (typeof functions.controller == 'function') functions.controllers = [functions.controller]
        if (!Array.isArray(functions.controllers)) functions.controllers = []
        if (typeof functions.observer == 'function') functions.observers = [functions.observer]
        if (!Array.isArray(functions.observers)) functions.observers = []

        if ('observable_'+key in object) {
            onlyBind(object, key, functions)
        } else {
            initializeAndBind(object, key, functions)
        }
    }

    nativeModel.unbindObserver = function(object, key, observer)
    {
        if (!('observable_'+key in object)) return
        if (typeof object != 'object' && typeof key != 'string' && typeof observer != 'function') return
        var observable = object['observable_'+key]
        var pos = observable.observers.indexOf(observer)
        if (pos != -1) observable.observers.splice(pos, 1)
    }

    nativeModel.hasObserver = function(object, key, observer)
    {
        if (!('observable_'+key in object)) return false
        if (typeof object != 'object' && typeof key != 'string' && typeof observer != 'function') return false
        var observable = object['observable_'+key]
        var pos = observable.observers.indexOf(observer)
        return (pos != -1)
    }

    nativeModel.unbindController = function(object, key, controller)
    {
        if (!('observable_'+key in object)) return
        if (typeof object != 'object' && typeof key != 'string' && typeof controller != 'function') return
        var observable = object['observable_'+key]
        var pos = observable.controllers.indexOf(controller)
        if (pos != -1) observable.controllers.splice(pos, 1)
    }

    nativeModel.hasController = function(object, key, observer)
    {
        if (!('observable_'+key in object)) return false
        if (typeof object != 'object' && typeof key != 'string' && typeof controller != 'function') return false
        var observable = object['observable_'+key]
        var pos = observable.controllers.indexOf(controller)
        return (pos != -1)
    }

    nativeModel.unbindAll = function(object, key)
    {
        if ('observable_'+key in object) {
            Object.defineProperty(object, key, {
                value: object['observable_'+key].value,
                writable: true,
                configurable: true,
                enumerable: true
            })
            delete object['observable_'+key]
        }
    }

    nativeModel.isObservable = function(object, key)
    {
        return 'observable_'+key in object
    }

}()