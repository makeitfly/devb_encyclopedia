(function ($, Drupal, drupalSettings) {
  $('span[data-hover-label]').each(function () {
    let labelId = $(this).data('hoverLabel').replace('label-', '');
    $('span[data-hover-label="label-' + labelId + '"]').hovercard({detailsHTML:$('span[data-hover-blurb="blurb-' + labelId + '"]').html()});
  });
})(jQuery, Drupal, drupalSettings);
