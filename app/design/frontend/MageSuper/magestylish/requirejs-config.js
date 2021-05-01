var config = {
  map: {
        "*": {
            "cdz_slider": "js/owlcarousel/owlslider",
            "modal" : "Magento_Ui/js/modal/modal",
        }
    },
    paths:  {
        "owlslider" : "js/owlcarousel/owl.carousel.min",
        'jquery/compat': 'js/compat',
        'jquery/jquery-migrate': 'js/jquery-migrate'
    },
    "shim": {
		"js/owlcarousel/owl.carousel.min": ["jquery"]
	},
};

