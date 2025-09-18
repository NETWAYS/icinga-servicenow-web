;(function (Icinga) {

    'use strict';

    var Servicenow = function (module) {
        this.module = module;
        this.initialize();
    };

    Servicenow.prototype = {
        initialize: function () {
            this.module.on('click', '.confirm-button', this.onClickConfirm, this);
        },

        /**
         * onClickConfirm shows a Browser confirmation windows.
         */
        onClickConfirm: function (event) {
            event.stopPropagation();
            let target = event.currentTarget;
            const confirmMsg = target.getAttribute('data-confirmation');

            return confirm(confirmMsg);
        }
    };

    Icinga.availableModules.servicenow = Servicenow;

}(Icinga));
