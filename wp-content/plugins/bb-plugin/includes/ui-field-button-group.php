<#
var atts     = "",
	field    = data.field,
	tooltip  = field.tooltip ? field.tooltip : {},
	multiple = false,
	min      = false,
	max      = false,
	cssClass = 'fl-button-group-field';

// Toggle data
if ( field.toggle ) {
	atts += " data-toggle='" + JSON.stringify( field.toggle ) + "'";
}

// Hide data
if ( field.hide ) {
	atts += " data-hide='" + JSON.stringify( field.hide ) + "'";
}

if ( true == field['multi-select'] || 'object' == typeof field['multi-select'] ) {
	multiple = true
}

if ( 'object' == typeof field['multi-select'] && 'min' in field['multi-select'] ) {
	min = field['multi-select'].min
}

if ( 'object' == typeof field['multi-select'] && 'max' in field['multi-select'] ) {
	max = field['multi-select'].max
}

if ( Object.keys(tooltip).length > 0 ) {
	cssClass += ' fl-button-group-field-tooltip'
}

#>
<div class="{{{cssClass}}}" data-multiple="{{{multiple}}}" data-min="{{{min}}}" data-max="{{{max}}}">
	<div class="fl-button-group-field-options">
		<# for ( var option in field.options ) {
			var selected = option === data.value ? 1 : 0;
		#>
		<# if ( option in tooltip ) { #>
			<div class="fl-button-group-tooltip-wrap">
		<# } #>
		<button
			class="fl-button-group-field-option"
			data-value="{{option}}"
			data-selected="{{selected}}"
		>
			{{{field.options[ option ]}}}
		</button>
			<# if ( option in tooltip ) { #>
					<span class="fl-button-group-tooltip">
						<span class="fl-button-group-tooltip-text">{{{tooltip[option]}}}</span>
					</span>
				</div>
			<# } #>
		<# } #>
	</div>
	<input type="hidden" name="{{data.name}}" value="{{data.value}}" {{{atts}}} />
	<div class="fl-clear"></div>
</div>
