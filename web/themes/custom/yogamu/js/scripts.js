(function ($, Drupal) {
  Drupal.behaviors.yogamu = {
    attach: function (context, settings) {
      // Trigger Bootstrap's offcanvas initialization
      $('[data-bs-toggle="offcanvas"]').offcanvas();

      // Check if the current page path is "/dashboard"
      if (window.location.pathname === '/dashboard') {
        // Trigger Bootstrap's show event to open the offcanvas
        $('.offcanvas').addClass('show');

        // Add class to body to adjust styles if necessary
        $('body').addClass('offcanvas-open');
      }
    }
  };
})(jQuery, Drupal);
