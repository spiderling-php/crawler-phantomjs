module.exports = (function () {
    'use strict'

    /**
     * Route
     * --------------------------------------------
     */

    var Route = function (method, path, response) {
        this.method = method
        this.regex = Route.pathToRegExp(path)
        this.response = response
    }

    Route.pathToRegExp = function (path) {
        path = path
            .replace('/', '\/')
            .replace(/:param/g, '([^\/]+)')
            .concat('\/?')

        return new RegExp('^' + path + '$', 'i')
    }

    Route.prototype.getParams = function (url) {
        return this.regex.exec(url).slice(1)
    }

    Route.prototype.match = function (method, url) {
        return this.method.toLowerCase() === method.toLowerCase() && this.regex.test(url)
    }

    var Router = function (routes) {
        this.routes = routes || []
    }

    /**
     * Router
     * --------------------------------------------
     */

    Router.prototype.add = function (method, path, callback) {
        this.routes.push(new Route(method, path, callback))

        return this
    }

    Router.prototype.get = function (path, callback) {
        return this.add('get', path, callback)
    }

    Router.prototype.post = function (path, callback) {
        return this.add('post', path, callback)
    }

    Router.prototype.delete = function (path, callback) {
        return this.add('delete', path, callback)
    }

    Router.prototype.match = function (method, url) {
        for (var i = 0; i < this.routes.length; i++) {
            if (this.routes[i].match(method, url)) {
                return this.routes[i]
            }
        }

        return false
    }

    Router.Route = Route

    return Router
})()
