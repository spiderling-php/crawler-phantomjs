module.exports = (function () {
    'use strict'

    /**
     * Arguments
     * --------------------------------------------
     */

    var Arguments = function (args, systemArgs) {
        var self = this

        Object.keys(args).forEach(function (key) {
            var regex = new RegExp('--' + key + '=([^ ]+)')

            self[key] = args[key]

            systemArgs.forEach(function (arg) {
                var match = regex.exec(arg)
                if (match !== null) {
                    self[key] = match[1]
                }
            })
        })
    }

    return Arguments
})()
