Drupal.leaflet.cacheManager = (function($) {
  "use strict";

  var cache = [];

  return {
    getCache: function() {
      return cache;
    },
    set: function(key, value) {
      if (typeof value != 'object') {
        return false;
      }

      cache[value.mn] = value;
    },
    get: function(key) {
      if (this.isRegistered(key)) {
        return cache[key];
      }
      return false;
    },
    isRegistered: function(key) {
      return (key in cache);
    }
  };
})(jQuery);
