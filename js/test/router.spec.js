(function () {
    'use strict'

    var chai = require('chai')
    var expect = chai.expect
    var Router = require(__dirname + '/../src/router')

    chai.use(require('dirty-chai'))

    describe('Route', function () {
        it('should be able to convert paths to routes', function () {
            var regex = Router.Route.pathToRegExp('/element/:param/attribute/:param')

            expect(regex.toString())
                .to.equal('/^\\/element\\/([^\\/]+)\\/attribute\\/([^\\/]+)\\/?$/i')
        })

        it('should match correct url', function () {
            var fn = function () {
            }

            var route1 = new Router.Route('get', '/element/:param/attribute/:param', fn)
            var route2 = new Router.Route('post', '/element/:param', fn)
            var route3 = new Router.Route('post', '/url', fn)

            expect(route1.match('get', '/elements'))
                .to.be.false()

            expect(route1.match('post', '/element/10/attribute/black'))
                .to.be.false()

            expect(route1.match('get', '/element/10/attribute/black'))
                .to.be.true()

            expect(route2.match('post', '/element/10/'))
                .to.be.true()

            expect(route3.match('POST', '/url'))
                .to.be.true()

        })

        it('should extract parameters with getParams method', function () {
            var fn = function () {
            }

            var route = new Router.Route('get', '/element/:param/attribute/:param', fn)

            expect(route.getParams('/element/10/attribute/black'))
                .to.deep.equal(['10', 'black'])
        })
    })

    describe('Router', function () {
        it('should be able to add routes with various methods', function () {
            var router = new Router()
            var fn = function () {
            }

            router.add('get', '/settings', fn)
            router.get('/clear-cookies', fn)
            router.post('/elements', fn)
            router.delete('/session', fn)

            expect(router.routes)
                .to.have.length.be(4)
                .to.contain(new Router.Route('get', '/settings', fn))
                .to.contain(new Router.Route('get', '/clear-cookies', fn))
                .to.contain(new Router.Route('post', '/elements', fn))
                .to.contain(new Router.Route('delete', '/session', fn))
        })

        it('should be able match a url and method', function () {
            var router = new Router()
            var fn = function () {
            }

            router.get('/element/:param/name', fn)
            router.post('/elements', fn)

            expect(router.match('GET', '/element/10/name'))
                .to.equal(router.routes[0])

            expect(router.match('POST', '/elements'))
                .to.equal(router.routes[1])

            expect(router.match('GET', '/session'))
                .to.be.false()
        })
    })
})()
