/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */

var soisyInstalmentBlock = Class.create();
soisyInstalmentBlock.prototype = {
    initialize: function (amount, url, blockClass, textPath) {
        this.amount = amount;
        this.url = url;
        this.blockClass = blockClass;
        this.textPath = textPath;
        this.request();
    },

    request: function () {
        new Ajax.Request(this.url, {
            method: 'POST',
            parameters: {
                "amount": this.amount,
                "text": this.textPath
            },
            requestHeaders: {Accept: 'application/json'},
            onSuccess: function (transport) {
                if (transport.responseText) {
                    this.placeBlock(transport.responseText);
                }
            }.bind(this)
        });
    },

    placeBlock: function (text) {

        $$('.' + this.blockClass).each(
            function (element) {
                if (element != undefined) {
                    element.insert({'after': "<p>" + text + "</p>"})
                }
            }
        );
    }
};

InstalmentSelector = Class.create();
InstalmentSelector.prototype = {
    initialize: function (instalmentEL, instalmentConfig, url, amount, amountEL, amountText) {
        this.instalmentSelectEL = $(instalmentEL);
        this.options = instalmentConfig.options;
        this.default = instalmentConfig.preselected;
        this.url = url;
        this.amount = amount;
        this.instalment = null;
        this.paymentAmount = $$(amountEL);
        this.amountText = amountText;
        this._addOptions();
        this._requestAmountByInstalment();

        Event.observe( this.instalmentSelectEL, 'change', this.update.bind(this));
    },

    _addOptions: function () {
        for (var instalment of this.options) {
            option = document.createElement('OPTION');
            option.value = instalment;
            option.text = instalment.stripTags();
            option.title = instalment;
            if (instalment == this.default) {
                this.instalment = instalment;
                option.selected = true;
            }
            this.instalmentSelectEL.appendChild(option);
        }
    },

    update: function () {

        if (this.instalment != this.getSelectedOptionValue()) {
            this.instalment = this.getSelectedOptionValue();
            this._requestAmountByInstalment();
        }
    },

    _requestAmountByInstalment: function () {
        new Ajax.Request(this.url, {
            method: 'POST',
            parameters: {
                "amount": this.amount,
                "instalments": (this.instalment) ? this.instalment : this.getSelectedOptionValue()
            },
            requestHeaders: {Accept: 'application/json'},
            onSuccess: function (transport) {
                console.log(transport.responseJSON);
                this.paymentAmount.first().innerHTML = transport.responseJSON.instalmentAmount + ' ' + this.amountText;
            }.bind(this)
        });
    },

    getSelectedOptionValue: function () {
        return this.instalmentSelectEL.getValue();
    }
};