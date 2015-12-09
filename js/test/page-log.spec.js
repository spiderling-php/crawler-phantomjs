(function () {
    'use strict'

    var chai = require('chai')
    var expect = chai.expect
    var PageLog = require(__dirname + '/../src/page-log')

    chai.use(require('dirty-chai'))

    describe('PageLog', function () {
        it('Should use page log callbacks to gather messages', function () {
            var page = {}
            var args = new PageLog(page)

            expect(args.getMessages()).to.be.empty()

            page.onConsoleMessage('some message')
            page.onConsoleMessage('other message')

            expect(args.getMessages()).to.deep.equal(
                ['some message', 'other message']
            )
        })

        it('Should use page log callbacks to gather errors', function () {
            var page = {}
            var args = new PageLog(page)

            expect(args.getErrors()).to.be.empty()

            page.onResourceError({ url: 'https://example.com', errorString: 'SSL error' })
            page.onError('division by zero', [{ file: 'https://example.com', line: 1 }])

            expect(args.getErrors()).to.deep.equal(
                [
                    'Error loading https://example.com: SSL error',
                    'division by zero in https://example.com:1'
                ]
            )
        })

        it('Should be able to clear messages and errors', function () {
            var page = {}
            var args = new PageLog(page)

            page.onConsoleMessage('some message')
            page.onError('division by zero', [{ file: 'https://example.com', line: 1 }])

            args.clear()

            expect(args.getMessages()).to.be.empty()
            expect(args.getErrors()).to.be.empty()
        })
    })
})()
