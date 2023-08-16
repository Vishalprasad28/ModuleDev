(function($, Drupal){
  Drupal.behaviors.myModuleBehavior = {
    attach: function(context, settings) {
      $('#phone-field').keyup(function() {
        let length = $(this).val().length;
        if (length == 10) {
          $(this).attr('type', 'password');
          // $(this).mask("(xxx) xxx-xxxx");
        }
        else {
          $(this).attr('type', 'text');
        }
      });
    }
  };
})(jQuery, Drupal)
