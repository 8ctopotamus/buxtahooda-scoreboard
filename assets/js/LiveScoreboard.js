( function ( $ ) {
  $.liveScoreboard = function(el, options) {
    var self = this;
    var $element = $(el);
    var config;
    
    var defaults = {
      IDS: {},
      CLASSES: {},
      SELECTORS: {
        'teamName' : '.team-name-header',
        'total'    : '.total-header',
        'headerLink' : '.header-link',
        'scoreboard' : '.scoreboard',
        'loader' : '.loader'      
      },
      ATTRIBUTES: {},
      ELEMENTS: {},
      
      ajaxUrl : false,
      mobile  : false
    };
    
    var cl = function (object) {
      try { console.log(object) } catch (e) {};
    }

    var init = function () {
      config = $.extend( true, defaults, options || {} ); 
      attachListeners();  
    };
    
    self.reload = function () {
      var postData = {
        'action' : 'reload_scoreboard'
      };
      
      if (config.mobile) {
        postData['mobile'] = true;
      }      
    
      $(config.SELECTORS.scoreboard, $element).hide();
      $(config.SELECTORS.loader, $element).show();
      
      $.post(config.ajaxUrl, postData, refreshScoreboard);    
    };

    var setup = function () {};

    var sortByColumn = function (e) {    
      e.preventDefault();
      var element = $(e.currentTarget);
      
      var sortBy = element.attr('data-sort-by');
      if (sortBy == 'game') {
        var gameId = element.attr('data-game-id');
      } 
      
      var postData = {
        'action' : 'reload_scoreboard',
        'sort' : element.attr('data-sort-by')
      };

      if(element.attr('data-game-id')) {
        postData['game'] = gameId;
      }
      
      if (config.mobile) {
        postData['mobile'] = true;
      }
            
      $(config.SELECTORS.scoreboard, $element).hide();
      $(config.SELECTORS.loader, $element).show();
      
      $.post(config.ajaxUrl, postData, refreshScoreboard);            
    };
    
    var refreshScoreboard = function(result) {
      newTable = $(result);
      
      $(config.SELECTORS.headerLink, newTable).on('click', sortByColumn);
      $(config.SELECTORS.scoreboard, $element).html(newTable);
      
      $(config.SELECTORS.loader, $element).hide();
      $(config.SELECTORS.scoreboard, $element).fadeIn();     
    }
    
    var attachListeners = function () {
      $(config.SELECTORS.headerLink, $element).on('click', sortByColumn);      
    };
    
    return init();
  }

  var methods = { 
    init: function(options) {
      return this.each(function () {
        var element = $(this);
        if (element.data( "liveScoreboard" ) )
          return;

        var pluginObject = new $.liveScoreboard(this, options);
        element.data("liveScoreboard", pluginObject);
      });
    },
    reload: function() {
      var element = $(this);
      var data = element.data("liveScoreboard").reload();
    }
  };

  $.fn.liveScoreboard = function( method ) {
    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.liveScoreboard' );
    }    
  };
})( jQuery );