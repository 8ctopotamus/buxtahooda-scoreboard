( function ( $ ) {
  function LiveScoreboardRef(el, options) {
    var self = this;
    var $element = $(el);
    
    var defaults = {
      IDS: {},
      CLASSES: {},
      SELECTORS: {
        'match'     : ".match",
        'matches'   : ".matches",
        'venueContainers' : ".venue-container",
        'venueLink' : ".venue-link",
        'matchInfo' : ".info",
        'matchUpdate' : ".update",
        'cancelButton' : ".cancel-button",
        'saveButton' : ".save-button",
        'matchForm'  : ".match-form",
        'loader'     : ".loader"
      },
      ATTRIBUTES: {},
      ELEMENTS: {}
    };
    
    var _venueContainers;
    var _matches;
    var _activeVenue;
    
    var cl = function (object) {
      try { console.log(object) } catch (e) {};
    }

    var init = function () {
      config = $.extend( true, defaults, options || {} );
      
      setup();
      attachListeners();  
      
      if (_venueContainers.size() == 1) {
        $(config.SELECTORS.matches, _venueContainers).toggle();
      }      
    };

    var setup = function () {
       _venueContainers = $(config.SELECTORS.venueContainers);
       _matches = $(config.SELECTORS.match, $element);
    };

    var attachListeners = function () {
      if (_venueContainers.size() > 1) {
        $(config.SELECTORS.venueLink).on('click', clickVenueLink);
      }
      $(config.SELECTORS.matchInfo, _matches).on('click', clickInfo);

      $(config.SELECTORS.cancelButton, _matches).on('click', clickCancel);
      $(config.SELECTORS.saveButton, _matches).on('click', clickSave);
    };
    
    var clickSave = function (e) {
      e.preventDefault();
      
      var element = $(e.currentTarget);
      var matchId = element.attr("data-match-id");
      var match = $("#"+matchId);      
      var form = $(config.SELECTORS.matchForm, match);
    
      var formData = form.serializeArray();
      var data = {
        'action' : 'update_match'
      };
      
      $.each(formData, function (index, input) {
        data[input.name] = input.value;
      });
      
      _activeVenue = $("#venue-"+data['venue_id']);
      
      $(config.SELECTORS.matchUpdate, match).hide();
      $(config.SELECTORS.loader, match).show();
      $.post('/wp-admin/admin-ajax.php', data, ajaxUpdateResult);
    } 
    
    var ajaxUpdateResult = function(data) {
      data = $(data);
            
      _activeVenue.replaceWith(data);
      $(config.SELECTORS.matches).fadeIn();
      
      try {
        $('#liveScoreboard').liveScoreboard('reload');
      } catch (e) {
        cl(e);
      }
      
      $(config.SELECTORS.matchInfo, data).on('click', clickInfo);

      $(config.SELECTORS.cancelButton, data).on('click', clickCancel);
      $(config.SELECTORS.saveButton, data).on('click', clickSave);
    } 
    
    var clickCancel = function (e) {
      e.preventDefault();
      
      var element = $(e.currentTarget);
      var matchId = element.attr("data-match-id");
      var match = $("#"+matchId);
      
      $(config.SELECTORS.matchUpdate, match).hide();      
      $(config.SELECTORS.matchInfo, match).show();
    };
    
    var clickInfo = function (e) {
      e.preventDefault();
    
      var element = $(e.currentTarget);
      var parent = element.parent();
            
      $(config.SELECTORS.matchInfo, parent).hide();
      $(config.SELECTORS.matchUpdate, parent).show();
    };

    var clickVenueLink = function(e) {
      e.preventDefault();
          
      var element = $(e.currentTarget);
      var parent  = element.parent();
      
      $(config.SELECTORS.matches ,parent).toggle();      
    };

    return init();
  }

  var methods = { 
    init: function(options) {
      return this.each(function () {
        var element = $(this);
        if (element.data( "LiveScoreboardRef" ) )
          return;

        var pluginObject = new LiveScoreboardRef(this, options);
        element.data("LiveScoreboardRef", pluginObject);
      });
    }
  };

  $.fn.liveScoreboardRef = function( method ) {
    if ( methods[method] ) {
      return methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
    } else if ( typeof method === 'object' || ! method ) {
      return methods.init.apply( this, arguments );
    } else {
      $.error( 'Method ' +  method + ' does not exist on jQuery.liveScoreboardRef' );
    }    
  };
})( jQuery );