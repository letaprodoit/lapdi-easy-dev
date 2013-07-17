<div class="tspep_form_element" id="{$field.name}_container_div" style="">
	<label for="{$field.id}">{$field.label}</label>
	{if $field.type == 'INPUT'}
	   <input class="{$class}" id="{$field.id}" name="{$field.name}" value="{$field.value}" />
	{elseif $field.type == 'TEXTAREA'}
	   <textarea class="{$class}" id="{$field.id}" name="{$field.name}">{$field.value}</textarea>
	{elseif $field.type == 'SELECT'}
	   <select class="{$class}" name="{$field.id}" id="{$field.name}" >
	   		{foreach $field.options as $okey => $ovalue}
	   			<option class="level-0" value="{$ovalue}" {if $field.value == $ovalue}selected='selected'{/if}>{$okey}</option>
	   		{/foreach}
	   </select>
	{/if}
	<div class="clear"></div>
	<div id="error-message-name"></div>
</div>

