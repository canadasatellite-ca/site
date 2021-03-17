require([
    "jquery",
    "mage/url",
], function ($, urlBuilder) {
//<![CDATA[ 
    $(document).ready(function () {
        $('.FaqMainForm').hide(); 
        $('.primary.actions-primary .write').click(function(){
            $('.FaqMainForm').toggle(); 
        });
    });    
//]]>
});





