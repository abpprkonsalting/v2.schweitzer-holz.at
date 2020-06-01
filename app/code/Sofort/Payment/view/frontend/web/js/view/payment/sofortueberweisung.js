/**
 * Created by Stefan Jauck on 09/05/17.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list',
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'sofortueberweisung',
                component: 'Sofort_Payment/js/view/payment/method-renderer/sofortueberweisung-method'
            }
        );
        /** Add view logic here if needed */
        return Component.extend({
        });
    }
);