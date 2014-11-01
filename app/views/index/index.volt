<h1>Youtube News und Artikel</h1>
<div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
  <!-- Indicators -->
  <ol class="carousel-indicators">
    <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
    <li data-target="#carousel-example-generic" data-slide-to="1"></li>
  </ol>

  <!-- Wrapper for slides -->
  <div class="carousel-inner">
    <div class="item active">
      <img src="http://lorempixel.com/output/city-q-g-640-480-3.jpg" alt="Some Text">
      <div class="carousel-caption">
      </div>
    </div>
    <div class="item">
      <img src="http://lorempixel.com/output/technics-q-g-640-480-6.jpg" alt="Test">
      <div class="carousel-caption">
      </div>
    </div>
  </div>

  <!-- Controls -->
  <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
    <span class="glyphicon glyphicon-chevron-left"></span>
  </a>
  <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
    <span class="glyphicon glyphicon-chevron-right"></span>
  </a>
</div>
<h2>Willkommen</h2>
<p class="welcome">
	Willkommen im devStorm Forum, das offizelle Forum des YT-Channels!<br />
	Hier findest du zu allem etwas, von PhP, Css, Js &uuml;ber node.js bis hin zum Offtopic,<br />
	ist alles dabei.
	<br />
	<h2>Wie registriere ich mich ?</h2>
	<p>
		Entweder &uuml;ber Github(W.I.P) oder du erstellt einen devStorm-Account.<br />
		Danach kannst du das Forum in vollem Umfang nutzen.
	</p>
	<br />
	Bitte denke daran das wir auch nur <strike>Nerds</strike> Menschen, deswegen Teilt uns doch bitte mit wenn ihr entweder<br />
	<ul>
		<li>Einen Bug findet</li>
		<li>Unsauberkeit beim Styling</li>
		<li>Neue Ideen</li>
	</ul>
	
	<br /><br />
	Euer devStorm Team
</p>
<h2 class="newsetPosts">Neuste Posts</h2>
{% for post in posts %}

{% endfor %}