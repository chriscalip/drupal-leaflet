Drupal.leaflet.pluginManager = (function($) {
  "use strict";
  var plugins = [];
  return {
    attach: function(context, settings) {
      for (var i in plugins) {
        var plugin = plugins[i];
        if (typeof plugin.attach == 'function') {
          plugin.attach(context, settings);
        }
      }
    },
    detach: function(context, settings) {
      for (var i in plugins) {
        var plugin = plugins[i];
        if (typeof plugin.detach == 'function') {
          plugin.detach(context, settings);
        }
      }
    },
    alter: function(){
      // @todo: alter hook
    },
    getPlugin: function(factoryService) {
      if (this.isRegistered(factoryService)) {
        return plugins[factoryService];
      }
      return false;
    },
    getPlugins: function(){
      return Object.keys(plugins);
    },
    register: function(plugin) {
      if (typeof plugin != 'object') {
        return false;
      }

      if (!plugin.hasOwnProperty('fs') || typeof plugin.init != 'function') {
        return false;
      }
      plugins[plugin.fs.toLowerCase()] = plugin;
    },
    createInstance: function(factoryService, data) {
      var factoryService = factoryService.toLowerCase();

      if (!this.isRegistered(factoryService)) {
        return false;
      }

      try {
        var obj = plugins[factoryService].init(data);
      } catch(e) {
        // @todo: handler here.
      }

      if (typeof obj == 'object') {
        obj.mn = data.data.mn;
        return obj;
      }

      return false;
    },
    isRegistered: function(factoryService) {
      return (factoryService.toLowerCase() in plugins);
    }
  };
})(jQuery);