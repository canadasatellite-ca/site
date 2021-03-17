define([
    'jquery'
], function ($) {
    'use strict';

    $.fn.submitWithMultipleParamValues = function (name, values) {
        var that = this;

        this.find('input[type="hidden"]').remove();

        values.forEach(function (value) {
            var input = $("<input>").attr("type", "hidden").attr("name", name).val(value);
            $(that).append($(input));
        });

        this.submit();
    };

    var $btnActivate = $('.btn-activate');
    var $cbActivateSim = $('.cb-activate-sim');
    var $form = $('#activate_sim');


    $cbActivateSim.off('change');
    $cbActivateSim.on('change', function(e) {
        e.preventDefault();

        var simsAreSelected = $cbActivateSim.filter(':checked').length > 0;

        $btnActivate.prop('disabled', !simsAreSelected);
    });

    $btnActivate.off('click');
    $btnActivate.on('click', function (e) {
        e.preventDefault();

        var simIds = $cbActivateSim.filter(':checked').map(function (index, checkbox) {
            var $cbSim = $(checkbox);
            var $simRow = $cbSim.closest('tr');

            return $simRow.data('sim-id');
        }).toArray();
        
        $form.submitWithMultipleParamValues('sims[]', simIds);
    });
});