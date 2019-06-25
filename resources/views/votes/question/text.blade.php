<div class="field">
    <label for="q{{ $question->id }}" style="font-size:1em;">
        {{ $question->criteria }}
    </label>
    @if($question->response->count()>0)
        <input type="text" id="q{{ $question->id }}" name="answer[{{ $question->id }}][criteria]" value="{{ $question->response->first()->response }}">
    @else
        <input type="text" id="q{{ $question->id }}" name="answer[{{ $question->id }}][criteria]">
    @endif
    <input type="hidden" name="answer[{{ $question->id }}][question_id]" value="{{ $question->id }}">
</div>
