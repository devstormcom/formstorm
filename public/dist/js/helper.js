var Helper;

Helper = (function() {
  function Helper(name1) {
    this.name = name1;
  }

  Helper.prototype.createDialogLink = function(element, text, linkto) {
    var id;
    id = this.uniqueId();
    return $(element).append('<a id="devstorm_' + id + '" href="' + linkto + '">' + text + '</a>');
  };

  Helper.play = function(episode, name) {
    return console.log('Playing ' + episode + ' of ' + name);
  };

  Helper.prototype.playOn = function() {
    return console.log('unknown');
  };

  Helper.prototype.uniqueId = function(length) {
    var id;
    if (length == null) {
      length = 8;
    }
    id = "";
    while (id.length < length) {
      id += Math.random().toString(36).substr(2);
    }
    return id.substr(0, length);
  };

  return Helper;

})();
