require(
    [
        'jquery',
        'jquery/ui',
        'jquery/validate',
        'mage/translate'
    ], function($){
        $.validator.addMethod(
            'canadian-postcode',
            function (value, element) {

                var position = $(element).attr('id').split(':')[0];

                if (typeof position !== "undefined"){

                    var country, region, slectedRegion, postCodeChar, isValid = false;
                    country = $("#"+position+"\\:country_id").val();
                    region = $("#"+position+"\\:region_id").val();

                    if (country != 'CA') {
                        return true;
                    }

                    if (!region) {
                        return false;
                    }

                    ///Zip Code correction
                    if (/^\S$/.test(value.charAt(3))) {
                        value = value.substr(0, 3) + ' ' + value.substr(3);
                        $(element).val(value);
                    }

                    if (!/^[a-zA-Z]\d[a-zA-Z]\s\d[a-zA-Z]\d$/.test(value)) {
                        return false;
                    }

                    slectedRegion = $("#"+position+"\\:region_id option:selected").text();
                    postCodeChar = value.charAt(0).toUpperCase();

                    switch(slectedRegion) {
                        case "Alberta":
                            if (postCodeChar == 'T') {
                                isValid = true;
                            }
                            break;
                        case "British Columbia":
                            if (postCodeChar == 'V') {
                                isValid = true;
                            }
                            break;
                        case "Manitoba":
                            if (postCodeChar == 'R') {
                                isValid = true;
                            }
                            break;
                        case "Newfoundland and Labrador":
                            if (postCodeChar == 'A') {
                                isValid = true;
                            }
                            break;
                        case "New Brunswick":
                            if (postCodeChar == 'E') {
                                isValid = true;
                            }
                            break;
                        case "Nova Scotia":
                            if (postCodeChar == 'B') {
                                isValid = true;
                            }
                            break;
                        case "Northwest Territories":
                            if (postCodeChar == 'X') {
                                isValid = true;
                            }
                            break;
                        case "Nunavut":
                            if (postCodeChar == 'X') {
                                isValid = true;
                            }
                            break;
                        case "Ontario":
                            if (postCodeChar == 'K' || postCodeChar == 'L' || postCodeChar == 'M' || postCodeChar == 'N' || postCodeChar == 'P') {
                                isValid = true;
                            }
                            break;
                        case "Prince Edward Island":
                            if (postCodeChar == 'C') {
                                isValid = true;
                            }
                            break;
                        case "Quebec":
                            if (postCodeChar == 'G' || postCodeChar == 'H' || postCodeChar == 'J') {
                                isValid = true;
                            }
                            break;
                        case "Saskatchewan":
                            if (postCodeChar == 'S') {
                                isValid = true;
                            }
                            break;
                        case "Yukon Territory":
                            if (postCodeChar == 'Y') {
                                isValid = true;
                            }
                            break;
                    }
                    return isValid;
                }
                console.warn('Billing or Shipping position is undefined');
                return true;
            }
            ,$.mage.__('Invalid Postal Code for selected Province')
        );
    }
);