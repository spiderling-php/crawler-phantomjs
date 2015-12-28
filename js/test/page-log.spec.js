(function () {
    'use strict'

    var chai = require('chai')
    var expect = chai.expect
    var PageLog = require(__dirname + '/../src/page-log')

    chai.use(require('dirty-chai'))

    describe('PageLog', function () {
        it('Should use page log callbacks to gather messages', function () {
            var page = {}
            var pageLog = new PageLog(page)

            expect(pageLog.getMessages()).to.be.empty()

            page.onConsoleMessage('some message')
            page.onConsoleMessage('other message')

            expect(pageLog.getMessages()).to.deep.equal(
                ['some message', 'other message']
            )
        })

        it('Should use page log callbacks to gather errors', function () {
            var page = {}
            var pageLog = new PageLog(page)

            expect(pageLog.getErrors()).to.be.empty()

            page.onResourceError({ url: 'https://example.com', errorString: 'SSL error' })
            page.onError('division by zero', [{ file: 'https://example.com', line: 1 }])

            expect(pageLog.getErrors()).to.deep.equal(
                [
                    'Error loading https://example.com: SSL error',
                    'division by zero in https://example.com:1'
                ]
            )
        })

        it('Should use page log callbacks to gather alert message', function () {
            var page = {}
            var pageLog = new PageLog(page)

            expect(pageLog.getAlert()).to.be.null()

            page.onAlert('Some alert')

            expect(pageLog.getAlert()).to.equal('Some alert')
        })

        it('Should use page log callbacks to gather conrim messages and return appropriate response', function () {
            var page = {}
            var pageLog = new PageLog(page)

            expect(pageLog.getAlert()).to.be.null()

            expect(page.onConfirm('Some confirm')).to.be.true()

            expect(pageLog.getAlert()).to.equal('Some confirm')

            pageLog.setConfirm(false)

            expect(page.onConfirm('Some other')).to.be.false()

            expect(pageLog.getAlert()).to.equal('Some other')
        })

        it('Should be able to clear messages and errors', function () {
            var page = {}
            var pageLog = new PageLog(page)

            page.onConsoleMessage('some message')
            page.onError('division by zero', [{ file: 'https://example.com', line: 1 }])

            pageLog.clear()

            expect(pageLog.getMessages()).to.be.empty()
            expect(pageLog.getErrors()).to.be.empty()
        })
    })
})()
