@if ($errors->any())
        {!! implode('', $errors->all('<div class="error_message">:message</div>')) !!}
@endif

<style type="text/css">
	.error_message{
		color: red;
		font-size: 10pt;
		font-style: italic;
		margin-left: 10px;
	}
</style>