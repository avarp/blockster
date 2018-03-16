function TranslatorInJs(msg1, msg2, $n) {
    if (typeof msg2 == 'undefined' && typeof $n == 'undefined') {
        return (msg1 in this) ? this[msg1] : msg1 
    } else if (typeof $n == 'undefined') {
        var key = msg2+'\u0004'+msg1
        return (key in this) ? this[key] : msg1 
    } else {
        if (msg1 in this && 'pluralExpr' in this) {
            var $p = 0
            eval(this.pluralExpr)
            if ($p in this[msg1]) return this[msg1][$p]
            console.error('Plural form #'+$p+' is not defined for phrase "'+msg1+'"')
        } else {
            return $n > 1 ? msg2 : msg1
        }
    }
}