/**
 * @project: CartImport
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

define([
    'jquery',
    'LitExtension_CartImport/js/jquery.form.min'
], function($){
    $.extend({
        LeCaIp: function(options){
            var defaults = {
                url: '',
                formKey: '',
                menuSetup: '#menu-setup',
                menuConfig: '#menu-config',
                menuConfirm: '#menu-confirm',
                formSetupWrap: '#lecaip-setup',
                formCsvWrap: '#lecaip-csv',
                formConfigWrap: '#lecaip-config',
                formConfirmWrap: '#lecaip-confirm',
                formImportWrap: '#lecaip-import',
                formResumeWrap: '#lecaip-resume',
                formSetup: '#form-setup',
                formCsv: '#form-csv',
                formConfig: '#form-config',
                formConfirm: '#form-confirm',
                formImport: '#form-import',
                formResume: '#form-resume',
                formSetupId: 'form-setup',
                formCsvId: 'form-csv',
                formConfigId: 'form-config',
                formConfirmId: 'form-confirm',
                formImportId: 'form-import',
                formResumeId: 'form-resume',
                formSetupLoading: '#form-setup-loading',
                formConfigLoading: '#form-config-loading',
                formConfirmLoading: '#form-confirm-loading',
                formImportLoading: '#form-import-loading',
                formResumeLoading: '#form-resume-loading',
                formSetupSubmit: '#form-setup-submit',
                formConfigSubmit: '#form-config-submit',
                formConfirmSubmit: '#form-confirm-submit',
                formImportSubmit: '#form-import-submit',
                formResumeSubmit: '#form-resume-submit',
                formConfigBack: '#form-config-back',
                formConfirmBack: '#form-confirm-back',
                errorMsg: 'Request timeout or server isn\'t responding, please reload the page.',
                msgTryError: '<p class="error">Request timeout or server isn\'t responding, please try again.</p>',
                msgTryWarning: '<p class="warning">Please try again.</p>',
                msgTryImport: '<p class="success"> - Resuming import ...</p>',
                importText: 'Imported',
                errorText: 'Errors',
                processTaxes: '#process-taxes',
                processManufacturers: '#process-manufacturers',
                processCategories: '#process-categories',
                processProducts: '#process-products',
                processCustomers: '#process-customers',
                processOrders: '#process-orders',
                processReviews: '#process-reviews',
                tryImportTaxes: '#try-import-taxes',
                tryImportManufacturers: '#try-import-manufacturers',
                tryImportCategories : '#try-import-categories',
                tryImportProducts : '#try-import-products',
                tryImportCustomers : '#try-import-customers',
                tryImportOrders : '#try-import-orders',
                tryImportReviews: '#try-import-reviews',
                fnResume: 'clearStore',
                timeDelay: 2000,
                autoRetry: 30000
            };
            var settings = $.extend(defaults, options);

            function enabledMenu(elm) {
                $(elm).addClass('open');
            }

            function disabledMenu(elm) {
                $(elm).removeClass('open');
            }

            function convertFromToData(elm){
                var data = '';
                var element = $(elm);
                if(element.length !== 0){
                    data = element.serialize();
                }
                return data;
            }

            function insertFormKey(data){
                var new_data = data+'&form_key='+settings.formKey;
                return new_data;
            }

            function validateForm(form, form_id){
                var result = true;
                if($(form).length !== 0){
                    if($(form).valid()){
                        result = true;
                    }else {
                        result = false;
                    }
                }
                return result;
            }

            function resetValidateForm(form, form_id){
                return true;
            }

            function showConsoleLog(msg){
                var element = $('#lecm-import-log .lecm-console-log');
                if(element.length !== 0){
                    element.append(msg);
                    element.animate({scrollTop: element.prop("scrollHeight")});
                }
            }

            function showConsoleLogCsv(msg){
                var element = $('#lecm-csv-log .lecm-console-log');
                if(element.length !== 0){
                    element.append(msg);
                    element.animate({scrollTop: element.prop("scrollHeight")});
                }
            }

            function createLeCookie(value){
                var date = new Date();
                date.setTime(date.getTime()+(24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
                document.cookie = "le_cart_import_run="+value+expires+"; path=/";
            }

            function getLeCookie(){
                var nameEQ = "le_cart_import_run=";
                var ca = document.cookie.split(';');
                for(var i=0;i < ca.length;i++) {
                    var c = ca[i];
                    while (c.charAt(0)===' ') c = c.substring(1,c.length);
                    if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length,c.length);
                }
                return null;
            }

            function deleteLeCookie(){
                var date = new Date();
                date.setTime(date.getTime()+(-1*24*60*60*1000));
                var expires = "; expires="+date.toGMTString();
                document.cookie = "le_cart_import_run="+expires+"; path=/";
            }

            function checkLeCookie(){
                var check = getLeCookie();
                var result = false;
                if(check === '1'){
                    result = true;
                }
                return result;
            }

            function checkOptionDuplicate(elm){
                if($(elm).length < 1){
                    return true;
                }
                var check = new Array();
                var exists = false;
                $(elm).each(function(index, value){
                    var element = $(value);
                    var elm_val = element.val()
                    if(elm_val){
                        check[index] = elm_val;
                        exists = true;
                    }
                });
                if(!exists){
                    return false;
                }
                var result = true;
                check.forEach(function(value, index) {
                    check.forEach(function(value_tmp, index_tmp){
                        if(value_tmp === value && index !== index_tmp){
                            result = false;
                        }
                    });
                });
                return result;
            }

            function checkSelectEntity(){
                var result = false;
                if($('input:checkbox:checked', '#valid-entity-sel').length > 0){
                    $('.field-valid', '#valid-entity-sel').fadeOut();
                    result = true;
                }else {
                    $('.field-valid', '#valid-entity-sel').fadeIn();
                }
                return result;
            }

            function checkSelectLangDuplicate(){
                var check = checkOptionDuplicate('#valid-lang-dup select');
                if(check === true){
                    $('.field-valid', '#valid-lang-dup').hide();
                } else{
                    $('.field-valid', '#valid-lang-dup').show();
                }
                return check;
            }

            function checkSelectCatDuplicate(){
                var check = checkOptionDuplicate('#valid-cat-dup select');
                if(check === true){
                    $('.field-valid', '#lvalid-cat-dup').hide();
                } else{
                    $('.field-valid', '#valid-cat-dup').show();
                }
                return check;
            }

            function checkSelectAttrDuplicate(){
                var check = checkOptionDuplicate('#valid-attr-dup select');
                if(check === true){
                    $('.field-valid', '#valid-attr-dup').hide();
                } else{
                    $('.field-valid', '#valid-attr-dup').show();
                }
                return check;
            }

            function checkElementShow(elm){
                var check = $(elm).is(':visible');
                return check;
            }

            function autoRetry(elm){
                if(settings.autoRetry > 0){
                    setTimeout(function(){triggerClick(elm)}, settings.autoRetry);
                }
            }

            function triggerClick(elm){
                var par_elm = elm+' .try-import';
                var check_show = checkElementShow(par_elm);
                var button = $(par_elm).children('div');
                if(check_show){
                    button.trigger('click');
                }
            }

            function showTryAgainImport(elm){
                var element = $(elm).find('.try-import');
                if(element.length > 0){
                    element.show();
                }
                deleteLeCookie();
            }

            function hideTryAgainImport(elm){
                var element = $(elm).find('.try-import');
                if(element.length > 0){
                    element.hide();
                }
                createLeCookie(1);
            }

            function showProcessBar(elm, total, imported, error, point){
                var element = $(elm);
                if(element.length > 0){
                    showProcessBarConsole(element, total, imported, error);
                    showProcessBarWidth(element, point);
                }
            }

            function showProcessBarConsole(element, total, imported, error){
                var pbc = element.find('.console-log');
                if(pbc.length !== 0){
                    var html = 'Imported: '+imported+'/'+total+', Errors: '+error;
                    pbc.show();
                    pbc.html(html);
                } else {
                    return false;
                }
            }

            function showProcessBarWidth(element, point){
                var pbw = element.find('.process-bar-width');
                if(pbw.length !== 0 && point !== null){
                    pbw.css({
                        'display' :'block',
                        'width' : point+'%'
                    });
                } else {
                    return false;
                }
            }

            function storageCsv(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=csv'),
                    success: function(response, textStatus, jqXHR) {
                        if(response.msg != ''){
                            showConsoleLogCsv(response.msg);
                        }
                        if(response.result === 'success'){
                            $(settings.formCsvWrap).hide();
                            $(settings.formConfigWrap).show();
                            $(settings.formConfigWrap).html(response.html);
                            deleteLeCookie();
                        } else if(response.result === 'process'){
                            setTimeout(storageCsv, settings.timeDelay);
                        } else {
                            $('#try-import-csv').show();
                            if(settings.autoRetry > 0){
                                setTimeout(function(){
                                    var par_elm = '#try-import-csv';
                                    var check_show = checkElementShow(par_elm);
                                    if(check_show){
                                        $('#try-import-csv').trigger('click');
                                    }
                                }, settings.autoRetry);
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLogCsv(settings.msgTryError);
                        $('#try-import-csv').show();
                        if(settings.autoRetry > 0){
                            setTimeout(function(){
                                var par_elm = '#try-import-csv';
                                var check_show = checkElementShow(par_elm);
                                if(check_show){
                                    $('#try-import-csv').trigger('click');
                                }
                            }, settings.autoRetry);
                        }
                    }
                });
            }

            function clearStore(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=clear'),
                    success: function(response, textStatus, jqXHR) {
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            setTimeout(importCurrencies, settings.timeDelay);
                            $(document).find('#process-clear-data').hide();
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            $('#try-import-not-clear').show();
                        } else if(response.result === 'process'){
                            setTimeout(clearStore, settings.timeDelay);
                        } else {
                            setTimeout(importCurrencies, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        $('#try-clear-store').show();
                    }
                });
            }

            function importCurrencies(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=currencies'),
                    success: function(response, textStatus, jqXHR) {
                        setTimeout(importTaxes, settings.timeDelay);
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        setTimeout(importTaxes, settings.timeDelay);
                    }
                });
            }

            function importTaxes(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=taxes'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processTaxes);
                            autoRetry(settings.processTaxes);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processTaxes, response.taxes.total, response.taxes.imported, response.taxes.error, response.taxes.point);
                            setTimeout(importManufacturers, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processTaxes);
                            autoRetry(settings.processTaxes);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processTaxes, response.taxes.total, response.taxes.imported, response.taxes.error, response.taxes.point);
                            setTimeout(importTaxes, settings.timeDelay);
                        } else {
                            setTimeout(importManufacturers, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processTaxes);
                        autoRetry(settings.processTaxes);
                    }
                });
            }

            function importManufacturers(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=manufacturers'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processManufacturers);
                            autoRetry(settings.processManufacturers);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processManufacturers, response.manufacturers.total, response.manufacturers.imported, response.manufacturers.error, response.manufacturers.point);
                            setTimeout(importCategories, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processManufacturers);
                            autoRetry(settings.processManufacturers);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processManufacturers, response.manufacturers.total, response.manufacturers.imported, response.manufacturers.error, response.manufacturers.point);
                            setTimeout(importManufacturers, settings.timeDelay);
                        } else {
                            setTimeout(importCategories, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processManufacturers);
                        autoRetry(settings.processManufacturers);
                    }
                });
            }

            function importCategories(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=categories'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processCategories);
                            autoRetry(settings.processCategories);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processCategories, response.categories.total, response.categories.imported, response.categories.error, response.categories.point);
                            setTimeout(importProducts, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processCategories);
                            autoRetry(settings.processCategories);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processCategories, response.categories.total, response.categories.imported, response.categories.error, response.categories.point);
                            setTimeout(importCategories, settings.timeDelay);
                        } else {
                            setTimeout(importProducts, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processCategories);
                        autoRetry(settings.processCategories);
                    }
                });
            }

            function importProducts(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=products'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processProducts);
                            autoRetry(settings.processProducts);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processProducts, response.products.total, response.products.imported, response.products.error, response.products.point);
                            setTimeout(importCustomers, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processProducts);
                            autoRetry(settings.processProducts);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processProducts, response.products.total, response.products.imported, response.products.error, response.products.point);
                            setTimeout(importProducts, settings.timeDelay);
                        } else {
                            setTimeout(importCustomers, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processProducts);
                        autoRetry(settings.processProducts);
                    }
                });
            }

            function importCustomers(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=customers'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processCustomers);
                            autoRetry(settings.processCustomers);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processCustomers, response.customers.total, response.customers.imported, response.customers.error, response.customers.point);
                            setTimeout(importOrders, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processCustomers);
                            autoRetry(settings.processCustomers);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processCustomers, response.customers.total, response.customers.imported, response.customers.error, response.customers.point);
                            setTimeout(importCustomers, settings.timeDelay);
                        } else {
                            setTimeout(importOrders, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processCustomers);
                        autoRetry(settings.processCustomers);
                    }
                });
            }

            function importOrders(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=orders'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processOrders);
                            autoRetry(settings.processOrders);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processOrders, response.orders.total, response.orders.imported, response.orders.error, response.orders.point);
                            setTimeout(importReviews, settings.timeDelay);
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processOrders);
                            autoRetry(settings.processOrders);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processOrders, response.orders.total, response.orders.imported, response.orders.error, response.orders.point);
                            setTimeout(importOrders, settings.timeDelay);
                        } else {
                            setTimeout(importReviews, settings.timeDelay);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processOrders);
                        autoRetry(settings.processOrders);
                    }
                });
            }

            function importReviews(){
                createLeCookie(1);
                $.ajax({
                    url: settings.url,
                    type: 'post',
                    dataType: 'json',
                    data: insertFormKey('action=reviews'),
                    success: function(response, textStatus, jqXHR) {
                        if(!response){
                            showConsoleLog(settings.msgTryError);
                            showTryAgainImport(settings.processReviews);
                            autoRetry(settings.processReviews);
                            return false;
                        }
                        if(response.msg != ''){
                            showConsoleLog(response.msg);
                        }
                        if(response.result === 'success'){
                            showProcessBar(settings.processReviews, response.reviews.total, response.reviews.imported, response.reviews.error, response.reviews.point);
                            $(settings.formImportSubmit).show();
                            deleteLeCookie();
                        } else if(response.result === 'error'){
                            showConsoleLog(settings.msgTryWarning);
                            showTryAgainImport(settings.processReviews);
                            autoRetry(settings.processReviews);
                        } else if(response.result === 'process'){
                            showProcessBar(settings.processReviews, response.reviews.total, response.reviews.imported, response.reviews.error, response.reviews.point);
                            setTimeout(importReviews, settings.timeDelay);
                        } else {
                            $(settings.formImportSubmit).show();
                            deleteLeCookie();
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        showConsoleLog(settings.msgTryError);
                        showTryAgainImport(settings.processReviews);
                        autoRetry(settings.processReviews);
                    }
                });
            }


            function run(){
                deleteLeCookie();
                $(document).on('change', '#cart_type', function(){
                    var _this = $(this);
                    var cart_type = $('#cart_type').val();
                    _this.prop('disabled', true);
                    $.ajax({
                        url: settings.url,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            cart_type: cart_type,
                            action: 'displayUpload',
                            form_key: settings.formKey
                        },
                        success: function(response){
                            if(response.result === 'show'){
                                $('#file-upload').html(response.html);
                            } else {
                                alert(response.msg);
                            }
                            _this.prop('disabled', false);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert(settings.errorMsg);
                            _this.prop('disabled', false);
                        }
                    });
                });

                $(document).on('click', '#form-upload-submit', function(){
                    $(settings.formResumeWrap).hide();
                    var _this = $(this);
                    if(!validateForm(settings.formSetup, settings.formSetupId)){
                        return false;
                    }
                    var load_upload = $('#upload-loading');
                    var cart_type = $('#cart_type');
                    $('.uir').html("");
                    $(settings.formSetup).ajaxSubmit({
                        url: settings.url,
                        dataType: 'json',
                        beforeSubmit: function (formData, formObject, formOptions) {
                            formData.push({name: 'action', value : 'upload', type: 'hidden'},{name: 'form_key', value : settings.formKey, type: 'hidden'});
                            $('#upload-msg').html('');
                            $('#upload-msg').hide();
                            _this.hide();
                            load_upload.show();
                            cart_type.prop('disabled', true);
                        },
                        success: function(response) {
                            $.each(response.msg, function(item){
                                var elm = this.elm;
                                var msg = this.msg;
                                $(elm).html(msg);
                            });
                            _this.show();
                            load_upload.hide();
                            cart_type.prop('disabled', false);
                            $(settings.formSetupSubmit).css({display: 'inline-block'});
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            alert(settings.errorMsg);
                            _this.show();
                            load_upload.hide();
                            cart_type.prop('disabled', false);
                        }
                    });
                });

                $(document).on('click', settings.formSetupSubmit, function(){
                    if(!validateForm(settings.formSetup, settings.formSetupId)){
                        return false;
                    }
                    $(settings.formSetupLoading).show();
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        data: {
                            action: 'setup',
                            form_key: settings.formKey
                        },
                        dataType: 'json',
                        success: function(response){
                            $(settings.formSetupLoading).hide();
                            if(response.result === 'success'){
                                $(settings.formSetupWrap).hide();
                                enabledMenu(settings.menuConfig);
                                disabledMenu(settings.menuSetup);
                                $(settings.formCsvWrap).html(response.html);
                                $(settings.formCsvWrap).show();
                                setTimeout(storageCsv, settings.timeDelay);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown){
                            $(settings.formSetupLoading).hide();
                            alert(settings.errorMsg);
                        }
                    });
                });

                $(document).on('click', settings.formConfigBack, function(){
                    $(settings.formSetupWrap).show();
                    $(settings.formConfigWrap).hide();
                    disabledMenu(settings.menuConfig);
                    enabledMenu(settings.menuSetup);
                });

                $(document).on('click', '#select-all', function(){
                    $('#valid-entity-sel input:checkbox').prop('checked',this.checked);
                });

                $(document).on('click', '.lv1', function(){
                    var _this = $(this);
                    if (_this.prop('checked') === false) {
                        _this.parents('.lv0').find('.lv2').prop('checked', false);
                    }
                });

                $(document).on('click', '.lv2', function() {
                    var _this = $(this);
                    if (_this.prop('checked') === true) {
                        _this.parents('.lv0').find('.lv1').prop('checked', true);
                    }
                });

                $(document).on('click', '.form-checkbox', function(){
                    var _this = $(this);
                    _this.parent().children('input').trigger('click');
                });

                $(document).on('click', '#choose-seo', function(){
                    $('#seo_plugin').slideToggle();
                });

                $(document).on('click', settings.formConfigSubmit, function(){
                    if(validateForm(settings.formConfig, settings.formConfigId) === true
                        && checkSelectCatDuplicate() === true
                            //&& checkSelectAttrDuplicate() === true
                        && checkSelectLangDuplicate() === true
                        && checkSelectEntity() === true){
                        resetValidateForm(settings.formConfig, settings.formConfigId);
                        $(settings.formConfigLoading).show();
                        var data = convertFromToData(settings.formConfig);
                        data = insertFormKey(data);
                        $.ajax({
                            url: settings.url,
                            type: 'post',
                            dataType: 'json',
                            data: data,
                            success: function(response, textStatus, jqXHR) {
                                $(settings.formConfigLoading).hide();
                                if(response.result === 'success'){
                                    //enabledMenu(settings.menuConfirm);
                                    $(settings.formConfirmWrap).html(response.html);
                                    $(settings.formConfigWrap).hide();
                                    $(settings.formConfirmWrap).show();
                                } else {
                                    alert(response.msg);
                                }
                            },
                            error: function(jqXHR, textStatus, errorThrown) {
                                $(settings.formConfigLoading).hide();
                                alert(settings.errorMsg);
                            }
                        });
                    } else {
                        alert('To proceed, please check and correct your configurations highlighted in red.');
                    }
                });

                $(document).on('click', settings.formConfirmBack, function(){
                    //disabledMenu(settings.menuConfirm);
                    $(settings.formConfirmWrap).hide();
                    $(settings.formConfigWrap).show();
                });

                $(document).on('click', settings.formConfirmSubmit, function(){
                    $(settings.formConfirmLoading).show();
                    var data = convertFromToData(settings.formConfirm);
                    data = insertFormKey(data);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(settings.formConfirmLoading).hide();
                            if(response.result === 'success'){
                                $(settings.formImportWrap).html(response.html);
                                $(settings.formConfirmWrap).hide();
                                $(settings.formImportWrap).show();
                                enabledMenu(settings.menuConfirm);
                                disabledMenu(settings.menuConfig);
                                createLeCookie(1);
                                setTimeout(clearStore, settings.timeDelay);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(settings.formConfirmLoading).hide();
                            alert(settings.errorMsg);
                        }
                    });
                });

                $(document).on('click', '#try-import-csv', function(){
                    createLeCookie(1);
                    $(this).hide();
                    setTimeout(storageCsv, settings.timeDelay);
                });

                $(document).on('click', '#try-clear-store', function(){
                    createLeCookie(1);
                    $(this).hide();
                    setTimeout(clearStore, settings.timeDelay);
                });

                $(document).on('click', settings.tryImportCategories, function(){
                    importCategories();
                    hideTryAgainImport(settings.processCategories);
                });

                $(document).on('click', settings.tryImportProducts, function(){
                    importProducts();
                    hideTryAgainImport(settings.processProducts);
                });

                $(document).on('click', settings.tryImportCustomers, function(){
                    importCustomers();
                    hideTryAgainImport(settings.processCustomers);
                });

                $(document).on('click', settings.tryImportOrders, function(){
                    importOrders();
                    hideTryAgainImport(settings.processOrders);
                });

                $(document).on('click', settings.tryImportTaxes, function(){
                    importTaxes();
                    hideTryAgainImport(settings.processTaxes);
                });

                $(document).on('click', settings.tryImportManufacturers, function(){
                    importManufacturers();
                    hideTryAgainImport(settings.processManufacturers);
                });

                $(document).on('click', settings.tryImportReviews, function(){
                    importReviews();
                    hideTryAgainImport(settings.processReviews);
                });

                $(document).on('click', settings.formImportSubmit, function(){
                    $(settings.formImportLoading).show();
                    var data = convertFromToData(settings.formImport);
                    data = insertFormKey(data);
                    var _this = $(this);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(settings.formImportLoading).hide();
                            _this.hide();
                            showConsoleLog(response.msg);
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(settings.formImportLoading).hide();
                            _this.hide();
                            showConsoleLog(settings.msgTryError);
                        }
                    });
                });

                $(window).on('beforeunload', function(){
                    var check = checkLeCookie();
                    if(check === true){
                        return "Migration is in progress, leaving current page will stop it! Are you sure want to stop?";
                    }
                });

                $(document).on('click', settings.formResumeSubmit, function(){
                    $(settings.formResumeLoading).show();
                    var data = convertFromToData(settings.formResume);
                    data = insertFormKey(data);
                    $.ajax({
                        url: settings.url,
                        type: 'post',
                        dataType: 'json',
                        data: data,
                        success: function(response, textStatus, jqXHR) {
                            $(settings.formResumeLoading).hide();
                            if(response.result === 'success'){
                                $(settings.formResumeWrap).hide();
                                $(settings.formSetupWrap).hide();
                                $(settings.formImportWrap).html(response.html);
                                $(settings.formImportWrap).show();
                                setTimeout(eval(settings.fnResume), settings.timeDelay);
                                createLeCookie(1);
                            } else {
                                alert(response.msg);
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            $(settings.formResumeLoading).hide();
                            alert(settings.errorMsg);
                        }
                    });
                });
            }

            return run();
        }
    });
});
