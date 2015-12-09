(function () {
    'use strict'

    var chai = require('chai')
    var expect = chai.expect
    var jsdom = require('jsdom')
    var PhantomEnv = require(__dirname + '/../src/phantom-env')

    chai.use(require('dirty-chai'))

    describe('PhantomEnv', function () {
        describe('.constructor', function () {
            it('should construct the PhantomEnv object properly', function () {
                var env = new PhantomEnv({})

                expect(env.items)
                  .to.be.an('array')
                  .and.to.be.empty()
            })
        })

        describe('.getId', function () {
            it('should add item if not found and cache it', function () {
                var env = new PhantomEnv({})
                var test1 = { id: 'test1' }

                expect(env.getId(test1)).to.equal(0)
                expect(env.items).to.contain(test1)
                expect(env.getId(test1)).to.equal(0)
            })

            it('should be able to add multiple items', function () {
                var env = new PhantomEnv({})
                var test1 = { id: 'test1' }
                var test2 = { id: 'test2' }

                expect(env.getId(test1)).to.equal(0)
                expect(env.getId(test2)).to.equal(1)

                expect(env.items).to.contain(test1).and.contain(test2)

                expect(env.getId(test1)).to.equal(0)
                expect(env.getId(test2)).to.equal(1)
            })
        })

        describe('.getIds', function () {
            it('should return multiple ids given an array of elements', function () {
                var env = new PhantomEnv({})
                var test1 = { id: 'test1' }
                var test2 = { id: 'test2' }
                var test3 = { id: 'test3' }
                var test4 = { id: 'test4' }

                env.getId(test1)
                env.getId(test2)

                expect(env.getIds([test2, test3, test4]))
                  .to.deep.equal([1, 2, 3])
            })
        })

        describe('.getElement', function () {
            it('should return previously set items using their id', function () {
                var env = new PhantomEnv({})
                var test1 = { id: 'test1' }
                var test2 = { id: 'test2' }

                env.getId(test1)
                env.getId(test2)

                expect(env.getElement(0)).to.deep.equal(test1)
                expect(env.getElement(1)).to.deep.equal(test2)
            })

            it('should throw RangeError when no item present', function () {
                var env = new PhantomEnv({})

                expect(function () {
                    env.getElement(1)
                }).to.throw(RangeError, 'No item found with id 1')
            })
        })

        describe('.searchElements', function () {
            it('should perform xpath search on document body, if no context provided', function () {
                var document = jsdom.jsdom([
                    '<p>',
                        '<a id="first" class="the-link">other</a>',
                        '<a id="second" class="the-link">jsdom!</a>',
                    '</p>'
                ].join(''))

                var env = new PhantomEnv(document)
                var result = env.searchElements('.//*[@class="the-link"]')

                expect(result)
                    .to.be.an('array')
                    .to.have.length(2)

                expect(result[0].getAttribute('id'))
                    .to.equal('first')

                expect(result[1].getAttribute('id'))
                    .to.equal('second')
            })

            it('should perform xpath search on child element, if context provided', function () {
                var document = jsdom.jsdom([
                    '<p>',
                        '<a id="first" class="the-link">',
                            '<span id="this"></span>',
                        '</a>',
                        '<a id="second" class="the-link">jsdom!</a>',
                        '<span id="other"></span>',
                    '</p>'
                ].join(''))

                var env = new PhantomEnv(document)
                var anchor = document.getElementById('first')
                var anchorId = env.getId(anchor)
                var result = env.searchElements('.//span', anchorId)

                expect(result)
                    .to.be.an('array')
                    .to.have.length(1)

                expect(result[0].getAttribute('id'))
                    .to.equal('this')
            })
        })

        describe('.searchIds', function () {
            it('should call searchElements and return the ids of those elements', function () {
                var document = jsdom.jsdom([
                    '<p>',
                        '<a id="first" class="the-link">other</a>',
                        '<a id="second" class="the-link">jsdom!</a>',
                    '</p>'
                ].join(''))

                var env = new PhantomEnv(document)

                expect(env.searchIds('.//*[@class="the-link"]')).to.deep.equal([0, 1])
                expect(env.searchIds('.//*[@id="second" and @class="the-link"]')).to.deep.equal([1])
            })
        })

        describe('.isVisible', function () {
            it('should be able to distinguish between visible and invisible elements', function () {
                var env = new PhantomEnv({})
                var a1 = { offsetWidth: 0, offsetHeight: 0 }
                var a2 = { offsetWidth: 20, offsetHeight: 4 }

                var a1Id = env.getId(a1)
                var a2Id = env.getId(a2)

                expect(env.isVisible(a1Id)).to.be.false()
                expect(env.isVisible(a2Id)).to.be.true()
            })
        })

        describe('.getUniqueId', function () {
            it('should generate unique values', function () {
                var env = new PhantomEnv({})

                expect(env.getUniqueId()).to.not.equal(env.getUniqueId())
            })
        })

        describe('.newElementSelector', function () {
            it('should set a new unique id and return the selector for it', function () {
                var document = jsdom.jsdom(
                    '<input id="select1">'
                )

                var env = new PhantomEnv(document)
                var inputIds = env.searchIds('//input')
                var input = document.getElementById('select1')

                var selector = env.newElementSelector(inputIds[0])

                expect(document.querySelector(selector)).to.deep.equal(input)
            })
        })

        describe('.getValue', function () {
            it('should get the value from elements', function () {
                var env = new PhantomEnv({})
                var input = { value: 'some value' }
                var id = env.getId(input)

                expect(env.getValue(id)).to.equal('some value')
            })
        })

        describe('.setSelected', function () {
            it('change the value of its select input', function () {
                var document = jsdom.jsdom([
                    '<select id="select1">',
                        '<option id="option1" value="1">One</option>',
                        '<option id="option2" value="2">Two</option>',
                    '</select>'
                ].join(''))

                var env = new PhantomEnv(document)
                var optionIds = env.searchIds('//option')
                var select = document.getElementById('select1')

                env.setSelected(optionIds[0])
                expect(select.value).to.equal('1')

                env.setSelected(optionIds[1])
                expect(select.value).to.equal('2')
            })
        })
    })
})()
