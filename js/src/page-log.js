module.exports = (function () {
    'use strict'

    /**
     * PageLog
     * --------------------------------------------
     * Collect errors and console messages
     */

    var PageLog = function (page) {
        var self = this

        this.messages = []
        this.errors = []

        page.onError = function (message, trace) {
            self.errors.push(message + ' in ' + trace[0].file + ':' + trace[0].line)
        }

        page.onResourceError = function (resourceError) {
            self.errors.push('Error loading ' + resourceError.url + ': ' + resourceError.errorString)
        }

        page.onConsoleMessage = function (message) {
            self.messages.push(message)
        }
    }

    /**
     * Return messages
     */
    PageLog.prototype.getMessages = function () {
        return this.messages
    }

    /**
     * Return errors
     */
    PageLog.prototype.getErrors = function () {
        return this.errors
    }

    /**
     * Clear all
     */
    PageLog.prototype.clear = function () {
        this.messages = []
        this.errors = []
    }

    return PageLog
})()
