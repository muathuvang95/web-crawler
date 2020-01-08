<?php

get_header('webcrl');

?>
<div id="webcrl-page">
	<div id="webcrl-step1">
		<p class="source-url">
			<label>Source URL:<br>
				<input type="text" id="webcrl-source-url" value="">
			</label>
		</p>
		<p class="detail-url-pattern">
			<label>Detail URL Pattern:<br>
				<input type="text" id="webcrl-detail-url-pattern" value="">
			</label>
		</p>
		<button type="button" id="webcrl-scan" class="webcrl-button">Scan</button>
		<button type="button" id="webcrl-stop-scan" class="webcrl-button" disabled="disabled">Stop</button>
		<div class="webcrl-msg" id="webcrl-scan-message"></div>
	</div>
	<div class="webcrl-divi"></div>
	<div id="webcrl-step2">
		<p class="title-selector">
			<label>Title Selector:<br>
				<input type="text" id="webcrl-title-selector" value="">
			</label>
		</p>
		<p class="thumbnail-selector">
			<label>Thumbnail Selector:<br>
				<input type="text" id="webcrl-thumbnail-selector" value="">
			</label>
		</p>
		<p class="gallery-selector">
			<label>Gallery Selector:<br>
				<input type="text" id="webcrl-gallery-selector" value="">
			</label>
		</p>
		<p class="summary-selector">
			<label>Summary Selector:<br>
				<input type="text" id="webcrl-summary-selector" value="">
			</label>
		</p>
		<p class="content-selector">
			<label>Content Selector:<br>
				<input type="text" id="webcrl-content-selector" value="">
			</label>
		</p>
		<button type="button" id="webcrl-get-data" class="webcrl-button">Get data</button>
		<button type="button" id="webcrl-stop-get-data" class="webcrl-button">Stop</button>
		<div class="webcrl-msg" id="webcrl-get-data-message"></div>
	</div>
</div>
<?php

get_footer('webcrl');
