(function () {
    'use strict'

    var chai = require('chai')
    var expect = chai.expect
    var Arguments = require(__dirname + '/../src/arguments')

    chai.use(require('dirty-chai'))

    describe('Arguments', function () {
        it('Should be instantiated with default args', function () {
            var args = new Arguments({ port: '10', path: 'big pass' }, [])

            expect(args.port).to.equal('10')
            expect(args.path).to.equal('big pass')
        })

        it('Should override defaults with system arguments', function () {
            var args = new Arguments(
                { port: 5, path: 'big pass' },
                ['script.js', '--port=10', '--path=my-path', '--system-something=10']
            )

            expect(args.port).to.equal('10')
            expect(args.path).to.equal('my-path')
        })
    })
})()
