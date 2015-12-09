(function (root, factory) {
    'use strict'

    if (typeof module === 'object' && module.exports) {

        module.exports = factory()
    } else {
        root.PhantomEnv = factory()
    }
})(this, function () {
    'use strict'

    var Env = function (document) {
        this.document = document
        this.items = []
    }

    Env.prototype.getId = function (element) {
        var id = this.items.indexOf(element)

        if (id === -1) {
            id = this.items.length
            this.items.push(element)
        }

        return id
    }

    Env.prototype.getIds = function (array) {
        var self = this

        return array.map(function (item) {
            return self.getId(item)
        })
    }


    Env.prototype.getElement = function (id) {
        if (id >= this.items.length) {
            throw new RangeError('No item found with id ' + id)
        }

        return this.items[id]
    }

    Env.prototype.searchElements = function (xpath, contextId) {
        var context = typeof contextId === 'undefined'
            ? this.document.body
            : this.getElement(contextId)

        var result = this.document.evaluate(
            xpath,
            context,
            null,
            this.document.defaultView.XPathResult.ORDERED_NODE_ITERATOR_TYPE
        )

        var resultArray = []
        var item

        while ((item = result.iterateNext())) {
            resultArray.push(item)
        }

        return resultArray
    }

    Env.prototype.getUniqueId = function () {
        return '_' + Math.random().toString(36).substr(2, 9)
    }

    Env.prototype.newElementSelector = function (id) {
        var uniqueId = this.getUniqueId()
        this.getElement(id).setAttribute('phantom-id', uniqueId)

        return '[phantom-id="' + uniqueId + '"]'
    }

    Env.prototype.searchIds = function (xpath, contextId) {
        return this.getIds(this.searchElements(xpath, contextId))
    }

    Env.prototype.isVisible = function (id) {
        var element = this.getElement(id)

        return element.offsetWidth > 0 && element.offsetHeight > 0
    }

    Env.prototype.getValue = function (id) {
        return this.getElement(id).value
    }

    Env.prototype.setValue = function (id, value) {
        var element = this.getElement(id)

        element.focus()
        element.value = value
        element.blur()
    }

    Env.prototype.setSelected = function (id) {
        var option = this.getElement(id)
        var select = option.parentNode.tagName === 'OPTGROUP'
            ? option.parentNode.parentNode
            : option.parentNode

        if (option.selected === false) {
            select.focus()
            option.selected = true
            select.blur()

            select.dispatchEvent(
                new this.document.defaultView.Event('change')
            )
        }
    }

    return Env
})
