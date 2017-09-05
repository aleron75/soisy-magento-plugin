/**
 * @category Bitbull
 * @package  Bitbull_Soisy
 * @author   Martins Saukums <martins.saukums@bitbull.it>
 */

var soisyInstalmentBlock = Class.create();
soisyInstalmentBlock.prototype = {
    initialize: function (amount, url, blockClass) {
        this.amount = amount;
        this.url = url;
        this.blockClass = blockClass;
        this.request();
    },

    request: function () {
        new Ajax.Request(this.url, {
            method: 'POST',
            parameters: {"amount": this.amount},
            requestHeaders: {Accept: 'application/json'},
            onSuccess: function (transport) {
                if (transport.responseText) {
                    this.placeBlock(transport.responseText);
                }
            }.bind(this)
        });
    },

    placeBlock: function (text) {
        $$('.' + this.blockClass).first().insert({'after': "<p>" + text + "</p>"});
    }
};