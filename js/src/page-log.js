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
        this.alert = null
        this.confirm = true

        page.onError = function (message, trace) {
            self.errors.push(message + ' in ' + trace[0].file + ':' + trace[0].line)
        }

        page.onResourceError = function (resourceError) {
            self.errors.push('Error loading ' + resourceError.url + ': ' + resourceError.errorString)
        }

        page.onConsoleMessage = function (message) {
            self.messages.push(message)
        }

        page.onAlert = function (message) {
            self.alert = message
        }

        page.onConfirm = function (message) {
            self.alert = message

            return self.confirm
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
     * Return alert
     */
    PageLog.prototype.getAlert = function () {
        return this.alert
    }

    /**
     * Set how to respond to a confirm
     */
    PageLog.prototype.setConfirm = function (confirm) {
        this.confirm = confirm
    }

    /**
     * Clear all
     */
    PageLog.prototype.clear = function () {
        this.messages = []
        this.errors = []
        this.alert = null
        this.confirm = true
    }

    return PageLog
})()
