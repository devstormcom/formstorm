
/*
+-------------------------------------------------------+
| Stormform
| Copyright (C) devstorm 2014-2015
+--------------------------------------------------------+
| Filename: forum.coffe
| Author: Flavio Kleiber (flaver12)
+--------------------------------------------------------+
| This program is released as free software under the
| Affero GPL license. You can redistribute it and/or
| modify it under the terms of this license which you
| can read by viewing online
| at www.gnu.org/licenses/agpl.html. Removal of this
| copyright header is strictly prohibited without
| written permission from the original author(s).
+--------------------------------------------------------+
 */
var Forum;

Forum = (function() {
  function Forum() {}


  /*
  * Saves a comment
  *
  * @param  {integer} threadId  id of the thread
  * @param  {integer} userId    id of the user
  * @param  {integer} content   unparsed content
  * @return {boolean}
   */

  Forum.prototype.sendPost = function(threadId, userId, content) {
    return console.log("WORKS!");
  };

  return Forum;

})();
