(function ($, Drupal) {
  Drupal.behaviors.defaultBookableCalendarJS = {
    attach: function (context, settings) {
      $('#some-input', context).once('myCustomBehavior').each(function () {
        // Apply the myCustomBehaviour effect to the elements only once.
      });
    }
  };
})(jQuery, Drupal);
