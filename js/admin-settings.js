(function($) {
	"use strict";

	/**
	* check whether both the sandbox (test) mode and Stored Payments are selected,
	* show warning message if they are
	*/
	function checkSandbox() {
		var	useTest = ($("input[name='dps_pxpay_wp_ecommerce[useTest]']:checked").val() == "1");

		if (useTest) {
			$(".dpspxpay-wpsc-opt-admin-test").fadeIn();
		}
		else {
			$(".dpspxpay-wpsc-opt-admin-test").hide();
		}
	}

	$("input[name='dps_pxpay_wp_ecommerce[useTest]']").change(checkSandbox);

	checkSandbox();

})(jQuery);
