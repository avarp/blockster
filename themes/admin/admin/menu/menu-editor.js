function loadMenuEditor(containerElement, menuFromPhp, t){
////////////////////////////////////////////////////////////////////////////////

function setSelectedField(menu, isSelected) {
    menu.children.forEach(function(item) {
        item.selected = isSelected
        if (item.children.length > 0) setSelectedField(item, isSelected)
    })
}

function setExpandedField(menu, isExpanded) {
    menu.children.forEach(function(item) {
        item.expanded = isExpanded
        if (item.children.length > 0) setExpandedField(item, isExpanded)
    })
}

function getSelection(menu) {
    var selection = []
    menu.children.forEach(function(item, index) {
        if (item.selected) {
            selection.push([index])
        }
        if (item.children.length > 0) {
            subselection = getSelection(item)
            subselection.forEach(function(subitem) {
                subitem.unshift(index)
                selection.push(subitem)
            })
        }
    })
    return selection
}

function isSelectionContinuous(selection) {
    if (selection.length == 0) return false
    if (selection.length == 1) return true

    var sample = cloneArray(selection[0])
    for (var i=1; i<selection.length; i++) {
        sample[sample.length-1]++
        if (!isEqualArrays(sample, selection[i])) return false
    }
    return true
}

function getItem(menu, multiIndex) {
    var result = menu
    for (var i=0; i<multiIndex.length; i++) {
        result = result.children[multiIndex[i]]
        if (typeof result == 'undefined') break
    }
    return result
}

function setItem(menu, multiIndex, item) {
    var result = menu
    for (var i=0; i<multiIndex.length; i++) {
        result = result.children[multiIndex[i]]
        if (typeof result == 'undefined') return false
    }
    result = item
    return false
}

function getParent(menu, multiIndex) {
    multiIndex = cloneArray(multiIndex)
    multiIndex.pop()
    return getItem(menu, multiIndex)
}

function setParent(menu, multiIndex, item) {
    multiIndex = cloneArray(multiIndex)
    multiIndex.pop()
    return setItem(menu, multiIndex, item)
}

function lastOfArray(arr) {
    return arr[arr.length-1]
}

function cloneArray(arr) {
    var x = []
    arr.forEach(function(a){x.push(a)})
    return x
}

function isEqualArrays(arr1, arr2) {
    if (arr1.length != arr2.length) return false
    return arr1.every(function(a1, i){return a1 === arr2[i]})
}

function debounce(func, wait, immediate) {
    var timeout;
    return function() {
        var context = this, args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        }, wait);
        if (immediate && !timeout) func.apply(context, args);
    };
}

////////////////////////////////////////////////////////////////////////////////
var msg = localStorage.getItem('menuEditorInfoAfterReloading');
if (msg) {
    switch (msg) {
        case 'savedSuccessfully':
        pushNotification('success', t('Menu is saved'));
        break;

        case 'deletedSuccessfully':
        pushNotification('success', t('Translation was deleted'));
        break;

        case 'translationCreatedSuccessfully':
        pushNotification('success', t('Translation was created'));
        break;
    }
    localStorage.removeItem('menuEditorInfoAfterReloading');
}


setSelectedField(menuFromPhp, false);
setExpandedField(menuFromPhp, true);

var basicUrl = window.location.href.split('/');
basicUrl.pop();
basicUrl = basicUrl.join('/');

const
    PUSH_TO_ROOT = 0,
    INSERT_BEFORE = 1,
    INSERT_AFTER = 2,
    PUSH_TO_CHILDREN = 3,
    UPDATE_SELECTED = 4

var app = new Vue({
    el:containerElement,
    template:'#menu-editor-template',
    data:{
        menu:menuFromPhp,
        onSending:false,
        components:{},
        isSysnameUnique:true
    },
    
    methods:{
        bindComponent:function(component) {
            this.components[component.name] = component.methods;
        },
        
        clearSelection:function() {
            if (this.selection.length > 0) setSelectedField(this.menu, false)
        },
        selectAll:function() {
            setSelectedField(this.menu, true)
        },
        expandAll:function() {
            setExpandedField(this.menu, true)
        },
        collapseAll:function() {
            setExpandedField(this.menu, false)
        },
        indent:function() {
            var s = this.selection
            if (!s.isIndentable) return
            var newParent = cloneArray(s[0])
            newParent[newParent.length-1]--
            var newParent = getItem(this.menu, newParent)
            s.forEach(function(i){
                newParent.children.push(getItem(this.menu, i))
            }.bind(this))
            var oldParent = getParent(this.menu, s[0])
            oldParent.children.splice(lastOfArray(s[0]), s.length)
        },
        outdent:function() {
            var s = this.selection
            if (!s.isOutdentable) return
            var newParent = cloneArray(s[0])
            newParent.pop()
            var insertTo = newParent.pop() + 1
            var newParent = getItem(this.menu, newParent)
            s.forEach(function(i){
                newParent.children.splice(insertTo, 0, getItem(this.menu, i))
            }.bind(this))
            var oldParent = getParent(this.menu, s[0])
            oldParent.children.splice(lastOfArray(s[0]), s.length)
        },
        remove:function() {
            if (!confirm(t('Delete selected items?'))) return
            var s = this.selection
            for (var i=s.length-1; i>=0; i--) {
                var parent = getParent(this.menu, s[i])
                parent.children.splice(lastOfArray(s[i]), 1)
            }
        },
        openItemEditModal:function() {
            var item = getItem(this.menu, this.selection[0])
            this.components.itemEditor.open(item, this.selection.length)
        },
        openItemCreateModal:function() {
            this.components.itemEditor.open(false, this.selection.length)
        },
        openTranslationModal:function() {
            this.components.translationModal.open()
        },
           

        saveItem:function(item) {
            if (item.saveOption == UPDATE_SELECTED) {
                var existedItem = getItem(this.menu, this.selection[0])
                existedItem.id = item.id
                existedItem.label = item.label
                existedItem.href = item.href
                existedItem.customField = item.customField
                existedItem.accessLevel = parseInt(item.accessLevel)
            } else {
                var newItem = {
                    id:item.id,
                    label:item.label,
                    href:item.href,
                    customField:item.customField,
                    accessLevel:item.accessLevel,
                    children:[],
                    selected:false,
                    expanded:true
                }
                switch (item.saveOption) {
                    case PUSH_TO_ROOT:
                    this.menu.children.push(newItem)
                    break

                    case PUSH_TO_CHILDREN:
                    getItem(this.menu, this.selection[0]).children.push(newItem)
                    break

                    case INSERT_BEFORE:
                    var parent = getParent(this.menu, this.selection[0])
                    var i = lastOfArray(this.selection[0])
                    parent.children.splice(i, 0, newItem)
                    break

                    case INSERT_AFTER:
                    var parent = getParent(this.menu, this.selection[0])
                    var i = lastOfArray(this.selection[0]) + 1
                    parent.children.splice(i, 0, newItem)
                    break
                }
            }
        },

        send:function() {
            var timeout = setTimeout(function(){
                this.onSending = true
            }.bind(this), 500)
            var xhr = new XMLHttpRequest
            xhr.open('POST', '/ajax/admin/menu::saveMenu', true)
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onerror = function() {
                console.error(xhr.responseText)
                clearTimeout(timeout)
                this.onSending = false
                pushNotification('danger', t('Menu was not saved. Try to save it again.'))
            }.bind(this)
            xhr.onload = function() {
                var output
                try {
                    output = JSON.parse(xhr.responseText)
                } catch (e) {
                    xhr.onerror()
                    return
                }
                if (output.response > 0) {
                    localStorage.setItem('menuEditorInfoAfterReloading', 'savedSuccessfully')
                    window.location.href = basicUrl+'/'+output.response
                } else {
                    xhr.onerror()
                }
            }.bind(this)
            xhr.send('menu='+encodeURIComponent(JSON.stringify(this.menu)))
        },

        removeTranslation:function() {
            if (!confirm(t('Delete this translation?'))) return
            var timeout = setTimeout(function(){
                this.onSending = true
            }.bind(this), 500)
            var xhr = new XMLHttpRequest
            xhr.open('POST', '/ajax/admin/menu::deleteMenu', true)
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onerror = function() {
                console.error(xhr.responseText)
                clearTimeout(timeout)
                this.onSending = false
                pushNotification('danger', t('Server error. Translation was not deleted.'))
            }.bind(this)
            xhr.onload = function() {
                var output
                try {
                    output = JSON.parse(xhr.responseText)
                } catch (e) {
                    xhr.onerror()
                    return
                }
                localStorage.setItem('menuEditorInfoAfterReloading', 'deletedSuccessfully')
                if (output.response > 0) {
                    window.location.href = basicUrl+'/'+output.response
                } else {
                    window.location.href = basicUrl
                }
            }.bind(this)
            xhr.send('menuId='+this.menu.id)
        },

        createTranslation:function(translation) {
            var timeout = setTimeout(function(){
                this.onSending = true
            }.bind(this), 500)
            var xhr = new XMLHttpRequest
            xhr.open('POST', '/ajax/admin/menu::createTranslation', true)
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onerror = function() {
                console.error(xhr.responseText)
                clearTimeout(timeout)
                this.onSending = false
                pushNotification('danger', t('Server error. Translation was not created.'))
            }.bind(this)
            xhr.onload = function() {
                var output
                try {
                    output = JSON.parse(xhr.responseText)
                } catch (e) {
                    xhr.onerror()
                    return
                }
                if (output.response > 0) {
                    localStorage.setItem('menuEditorInfoAfterReloading', 'translationCreatedSuccessfully')
                    if (translation.open) {
                        window.location.href = basicUrl+'/'+output.response
                    } else {
                        window.location.href = window.location.href
                    }
                } else {
                    xhr.onerror()
                }
            }.bind(this)
            var postData = 'sysname='+this.menu.sysname+'&langId='+translation.langId;
            if (translation.duplicate) postData += '&duplicateFromLangId='+translation.duplicateFromLangId;
            xhr.send(postData)
        } 
    },

    computed:{
        selection:function() {
            var s = getSelection(this.menu)
            s.isContinuous = isSelectionContinuous(s)
            s.isIndentable = s.isContinuous && s.length > 0 && lastOfArray(s[0]) > 0
            s.isOutdentable = s.isContinuous && s.length > 0 && s[0].length > 1
            return s
        },

        errors:function() {
            var e = {
                length:0,
                sysname:'',
                name:''
            }
            if (this.menu.sysname == '') {
                e.sysname = t('Fill system name.')
                e.length++
            } else if (/^[a-z0-9\-_]+$/.test(this.menu.sysname) == false) {
                e.sysname = t('Use only a...z, 0...9, - and _')
                e.length++
            } else if (!this.isSysnameUnique) {
                e.sysname = t('This name is already in use.')
                e.length++
            }
            if (this.menu.name == '') {
                e.label = t('Fill name.')
                e.length++
            }
            return e
        }     
    },

    watch:{
        'menu.sysname': debounce(function(sysname) {
            if (sysname == '') {
                this.isSysnameUnique = true
                return
            }
            var xhr = new XMLHttpRequest
            xhr.open('POST', '/ajax/admin/menu::isSysnameUnique', true)
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded')
            xhr.onerror = function() {
                console.error(xhr.responseText)
                clearTimeout(timeout)
                this.onSending = false
                pushNotification('danger', t('Server error. Check connection.'))
            }.bind(this)
            xhr.onload = function() {
                var output
                try {
                    output = JSON.parse(xhr.responseText)
                } catch (e) {
                    xhr.onerror()
                    return
                }
                this.isSysnameUnique = output.response
            }.bind(this)
            var postData = 'sysname='+this.menu.sysname+'&langId='+this.menu.langId;
            xhr.send(postData)
        }, 500)
    },

    components:{

        'translation-modal':{
            name:'translation-modal',
            template: '#translation-modal-template',
            props:['name'],
            created: function() {
                this.$emit('created', {
                    name:this.name,
                    methods:{
                        open:this.open
                    }
                })
            },
            data:function() {
                var data = {
                    showErrors:false,
                    showInfo:true,
                    translation:{
                        langId:0,
                        open:false,
                        duplicate:false,
                        duplicateFromLangId:0
                    }
                }
                if (localStorage.getItem('translationModalHideInfo')) {
                    data.showInfo = false
                }
                return data
            },
            methods:{
                submit:function() {
                    if (this.errors.length > 0) {
                        this.showErrors = true
                    } else {
                        this.$emit('submit', this.translation)
                        this.close()
                    }
                },
                open:function() {
                    Mov.showModal({target:this.$el})
                },
                close:function() {
                    Mov.hideModal({target:this.$el}).then(function() {
                        this.translation.langId = 0
                        this.translation.open = false
                        this.translation.duplicate = false
                        this.translation.duplicateFromLangId = 0
                    }.bind(this));
                },
                hideInfo() {
                    this.showInfo = false;
                    localStorage.setItem('translationModalHideInfo', 'Y');
                }
            },
            computed:{
                errors:function() {
                    var e = []
                    if (this.translation.langId == 0) e.push(t('Select language of new translation'))
                    if (this.translation.duplicate == true && this.translation.duplicateFromLangId == 0) {
                        e.push(t('Select language you want to copy from.'))
                    }
                    return e
                }
            }
        },

        'item-editor':{
            name:'item-editor',
            template: '#item-editor-template',
            props:['name'],
            created: function() {
                this.$emit('created', {
                    name:this.name,
                    methods:{
                        open:this.open
                    }
                })                
            },
            data:function() {
                return {
                    PUSH_TO_ROOT:PUSH_TO_ROOT,
                    INSERT_BEFORE:INSERT_BEFORE,
                    INSERT_AFTER:INSERT_AFTER,
                    PUSH_TO_CHILDREN:PUSH_TO_CHILDREN,
                    UPDATE_SELECTED:UPDATE_SELECTED,
                    showErrors:false,
                    pushToRootOnly:true,
                    item:{
                        id:0,
                        label:'',
                        href:'',
                        customField:'',
                        accessLevel:0,
                        saveOption:PUSH_TO_ROOT,
                    }
                }
            },
            methods:{
                submit:function() {
                    if (this.errors.length > 0) {
                        this.showErrors = true
                    } else {
                        this.$emit('submit', this.item)
                        this.close()
                    }
                },
                open:function(item, selectionLength) {
                    if (item) {
                        this.item.id = item.id
                        this.item.label = item.label
                        this.item.href = item.href
                        this.item.customField = item.customField
                        this.item.accessLevel = item.accessLevel
                        this.item.saveOption = UPDATE_SELECTED
                    }
                    this.pushToRootOnly = selectionLength != 1
                    Mov.showModal({target:this.$el})
                },
                close:function() {
                    Mov.hideModal({target:this.$el}).then(function() {
                        this.item.id = 0
                        this.item.label = ''
                        this.item.href = ''
                        this.item.customField = ''
                        this.item.accessLevel = 0
                        this.item.saveOption = PUSH_TO_ROOT
                        this.showErrors = false
                        this.pushToRootOnly = true
                    }.bind(this));
                }
            },
            computed:{
                errors:function() {
                    var e = []
                    if (this.item.label == '') e.push(t('Label of item is empty'))
                    if (this.item.href == '') e.push(t('Address of link is empty'))
                    if (this.item.accessLevel === '') e.push(t('Access level is empty'))
                    return e
                }
            }
        },

        'tree-view':{
            name:'tree-view',
            template:'#tree-view-template',
            props:['list'],
            watch:{
                'list.length':{
                    handler: function() {
                        this.$nextTick(function() {
                            initDraggableList({
                                items: this.$el.children,
                                handler: '.drag-handle',
                                afterDrag: this.afterDrag
                            });
                        });
                    },
                    immediate: true
                }
            },
            methods:{
                expand:function(el, done) {
                    if (Mov.expandHeight({target:el, duration:150})) setTimeout(done, 150)
                    else done()
                },
                collapse:function(el, done) {
                    if (Mov.collapseHeight({target:el, duration:150})) setTimeout(done, 150)
                    else done()
                },
                afterDrag:function(i0, i1, isTouchEvent) {
                    if (i1 != i0) {
                        var b = this.list[i0]
                        this.list.splice(i0, 1)
                        this.list.splice(i1, 0, b)
                    }
                    // <dirty-hack> (because Chrome leave :hover state even element is moved away from cursor) 
                    if (!isTouchEvent && i1 != i0) {
                        var items = this.$el.children
                        items[i0].firstElementChild.style.background = 'transparent'
                        items[i1].firstElementChild.style.background = '#f8f8f8'
                        var f = function() {
                            items[i0].firstElementChild.style.background = ''
                            items[i1].firstElementChild.style.background = ''
                            document.body.removeEventListener('mousemove', f)
                        }
                        document.body.addEventListener('mousemove', f)
                    }
                    // </dirty-hack>
                }
            }
        }

    }
})

////////////////////////////////////////////////////////////////////////////////
}