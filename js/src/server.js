(function () {
    'use strict'

    var Router = require('./router')
    var Arguments = require('./arguments')
    var PageLog = require('./page-log')

    var server = require('webserver').create()
    var page = require('webpage').create()
    var router = new Router()
    var args = new Arguments(
        {
            port: 8281,
            env: 'phantom-env.js'
        },
        require('system').args
    )
    var pageLog = new PageLog(page)

    console.log('Starting server on port ' + args.port)

    /**
     * Viewport size with large height so we do not need to scroll around for click events
     */
    page.viewportSize = { width: 1024, height: 4000 }

    /**
     * Response classes
     */

    var Response = function (callback) {
        this.callback = callback
    }

    Response.prototype.evaluate = function (request, page) {
        return this.callback(request, page)
    }

    var PageResponse = function (callback) {
        this.callback = callback
    }

    PageResponse.prototype.evaluate = function (request, page) {
        var isEnvDefined = page.evaluate(function () {
            return typeof PhantomEnv !== 'undefined'
        })

        if (isEnvDefined === false) {
            console.debug(' ┗━ inject', 'PhantomEnv', page.url)
            page.injectJs(args.env)
            page.evaluate(function () {
                PhantomEnv = new PhantomEnv(document)
            })
        }

        return page.evaluate(this.callback, request, page)
    }

    /**
     * Configure routes
     */
    router

        .get('/settings', new Response(function (request, page) {
            return page.settings
        }))

        .get('/source', new Response(function (request, page) {
            return page.content
        }))

        .get('/session', new Response(function () {
            return true
        }))

        .delete('/session', new Response(function () {
            phantom.exit()
        }))

        .get('/errors', new Response(function () {
            return pageLog.getErrors()
        }))

        .get('/alert', new Response(function () {
            return pageLog.getAlert()
        }))

        .post('/confirm', new Response(function (request) {
            return pageLog.setConfirm(request.post.value)
        }))

        .get('/messages', new Response(function () {
            return pageLog.getMessages()
        }))

        .delete('/cookies', new Response(function () {
            phantom.clearCookies()
        }))

        .post('/screenshot', new Response(function (request, page) {
            page.viewportSize = { width: 1024, height: 800 }
            page.render(request.post.value)
            page.viewportSize = { width: 1024, height: 4000 }
        }))

        .get('/url', new Response(function (request, page) {
            return page.url
        }))

        .post('/url', new Response(function (request, page) {
            pageLog.clear()

            console.debug(' ┗━ opening', request.post.value)

            page.open(request.post.value, function (status) {
                console.debug(' ┗━ done', status)
            })
        }))

        .post('/execute', new PageResponse(function (request) {
            return (new Function(request.post.value))()
        }))

        .post('/elements', new PageResponse(function (request) {
            return PhantomEnv.searchIds(request.post.value)
        }))

        .post('/element/:param/elements', new PageResponse(function (request) {
            return PhantomEnv.searchIds(request.post.value, request.params[0])
        }))

        .get('/element/:param/name', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).tagName.toLowerCase()
        }))

        .get('/element/:param/attribute/:param', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).getAttribute(request.params[1])
        }))

        .get('/element/:param/html', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).outerHTML
        }))

        .get('/element/:param/text', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).textContent
        }))

        .get('/element/:param/value', new PageResponse(function (request) {
            return PhantomEnv.getValue(request.params[0])
        }))

        .post('/element/:param/value', new PageResponse(function (request) {
            return PhantomEnv.setValue(request.params[0], request.post.value)
        }))

        .get('/element/:param/visible', new PageResponse(function (request) {
            return PhantomEnv.isVisible(request.params[0])
        }))

        .get('/element/:param/selected', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).selected
        }))

        .get('/element/:param/checked', new PageResponse(function (request) {
            return PhantomEnv.getElement(request.params[0]).checked
        }))

        .post('/element/:param/select', new PageResponse(function (request) {
            return PhantomEnv.setSelected(request.params[0])
        }))

        .post('/element/:param/hover', new Response(function (request, page) {

            var getter = new PageResponse(function (request) {
                return PhantomEnv.getElement(request.params[0]).getBoundingClientRect()
            })

            var rect = getter.evaluate(request, page)

            page.sendEvent('mousemove', rect.left + rect.width / 2, rect.top + rect.height / 2)
        }))

        .post('/element/:param/click', new Response(function (request, page) {

            var getter = new PageResponse(function (request) {
                return PhantomEnv.getElement(request.params[0]).getBoundingClientRect()
            })

            var rect = getter.evaluate(request, page)

            page.sendEvent('click', rect.left + rect.width / 2, rect.top + rect.height / 2)
        }))

        .post('/element/:param/upload', new Response(function (request, page) {

            var getter = new PageResponse(function (request) {
                return PhantomEnv.newElementSelector(request.params[0])
            })

            var selector = getter.evaluate(request, page)

            page.uploadFile(selector, request.post.value)
        }))

    /**
     * Start the webserver on port
     */
    server.listen(args.port, function (request, response) {
        var route = router.match(request.method, request.url)

        if (route !== false) {
            console.debug(request.method, request.url)

            if (request.post) {
                console.debug(' ┗━ post━', request.post.value)
            }

            request.params = route.getParams(request.url)
            var data = route.response.evaluate(request, page)

            if (data) {
                console.debug(' ┗━ result━', data)
            }

            response.setHeader('Content-Type', 'application/json')
            response.write(JSON.stringify(data))

            response.statusCode = 200
        } else {
            console.error('Not found', request.method, request.url)
            response.write('')
            response.statusCode = 404
        }

        console.debug('')
        response.close()
    })
})()
