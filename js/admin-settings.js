"use strict";

(function ($) {
  /**
  * check whether both the sandbox (test) mode and Stored Payments are selected,
  * show warning message if they are
  */
  function checkSandbox() {
    var warning = $(".dpspxpay-wpsc-opt-admin-test");
    var useTest = $("input[name='dps_pxpay_wp_ecommerce[useTest]']:checked").val() === "1";

    if (useTest) {
      warning.fadeIn();
    } else {
      warning.hide();
    }
  }

  $("#wpsc_options_page").on("change", "input[name='dps_pxpay_wp_ecommerce[useTest]']", checkSandbox);
  checkSandbox();
})(jQuery);
