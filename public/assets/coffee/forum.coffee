###
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
###

class Forum

  ###
  * Saves a comment
  *
  * @param  {integer} threadId  id of the thread
  * @param  {integer} userId    id of the user
  * @param  {integer} content   unparsed content
  * @return {boolean}
  ###
  sendPost: (threadId, userId, content) ->
    $.ajax '/ajax/saveComment',
      type: 'POST'
      dataType: 'JSON'
      data: {'threadId': threadId, 'userId': userId, 'content': content}
      error: (jqXHR, textStatus, errorThrown) ->
        #On a error just return false
        return false
      success: (data, textStatus, jqXHR) ->
        #On success return true
        return true

  ###
  * Deletes a comment
  *
  * @param  {integer} postId  id of the post
  * @return {boolean}
  ###
  deletePost: (postId) ->
    $.ajax '/ajax/deleteComment',
      type: 'POST'
      dataType: 'JSON'
      data: {'postId': postId}
      error: (jqXHR, textStatus, errorThrown) ->
        #On a error just return false
        return false
      success: (data, textStatus, jqXHR) ->
        #On success return true
        return true




