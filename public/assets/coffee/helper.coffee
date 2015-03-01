#author Flavio Kleiber
#Some useful helpers

class Helper
  constructor: (@name) ->

    # Prototype method
  createDialogLink: (element, text, linkto) ->
    id = @uniqueId()
    $(element).append('<a id="devstorm_' + id + '" href="' + linkto + '">' + text + '</a>')

  # Static method
  @play: (episode, name) ->
    console.log 'Playing ' + episode + ' of ' + name

  playOn: ->
    console.log 'unknown'

  uniqueId: (length=8) ->
    id = ""
    id += Math.random().toString(36).substr(2) while id.length < length
    id.substr 0, length