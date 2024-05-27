@include('includes.headwhite')

<div class="container-fluid">


    <form action="/invoice-logo-process" method="post" enctype="multipart/form-data" class="form-horizontal">
        @csrf
        <div class="row form-group">
            <div class="col-2">
                <label for="logo_file" class=" form-control-label">File input</label>
            </div>
            <div class="col-4">
                <input type="file" name="logo_file" id="logo_file" name="logo_file" class="form-control-file" required><br>
                <button type="submit" class="btn btn-primary">Save This Logo</button>
            </div>
            @if (empty($file))
            <div class="col-6">
                <h4>Current Image</h4>
                <div id="logoimagehtml"><img src="/img/format001.jpg" id="logoimage"></div>
            </div>
            @else
            <div class="col-6">
                <h4>Current Image</h4>
                <div id="logoimagehtml"><img src="{{ $file }}" id="logoimage" style="max-height:100px"></div>
            </div>
            @endif
        </div>
        
    </form>


</div>

@include('includes.footer')

<script>
    $( document ).ready(function() {
        @if (empty($file))
        var htmldet = $('#logoimagehtml', parent.document).html();
        console.log(htmldet);
        $("#logoimagehtml").html(htmldet);
        @else
        $('#logoimagehtml', parent.document).html('<img src="{{ $file }}" id="logoimage" style="max-height:150px">');
        $('#invoice_logo', parent.document).val('{{ $file }}');
        @endif
    });
</script>

</body>

</html>
<!-- end document-->