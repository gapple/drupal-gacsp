/**
 * @file
 * Initialize analytics on the page.
 */
/* global ga*/

(function (drupalSettings) {
  'use strict';

  if (!drupalSettings.gacsp) {
    return;
  }

  /*eslint-disable */
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
  /*eslint-enable */

  for (var i = 0; i < drupalSettings.gacsp.commands.length; i++) {
    ga.apply(this, drupalSettings.gacsp.commands[i]);
  }

})(drupalSettings);
