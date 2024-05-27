@include('includes.head')
@include('includes.user-header')

<!-- PAGE CONTAINER-->
<div class="page-container">

    @include('includes.user-top')

    <!-- MAIN CONTENT-->
        <div class="main-content">
            {{-- {{print_r($data)}} --}}
            <div class="section__content section__content--p30">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-8">
                            <section>
                                <h3 class="sumb--title">File</h3>
                            </section>
                
                            <div class="container md-8">
                                <h3 class="text-center mb-8">Edit File</h3>
                            </div>
                            <div class="card push-top">
                                <div class="card-header">
                                  Edit & Update
                                </div>
                                <div class="card-body">
                                    @if(session()->has('completed')) <p class="alert alert-success">{{session('completed')}}</p> @endif 
                                    <form method="post" action="{{ route('DocumentUploadController.doc-edit-process', $data->id)}}" enctype="multipart/form-data">
                                        <div class="form-group">
                                            @csrf
                                            @method('PATCH')
                                            <label for="name">Name</label>                                            
                                            <input type="hidden" class="form-control" name="id" value="{{$data->id}}"/>
                                            <input type="text" class="form-control" name="name" value="{{$data->name}}"/>
                                        </div>
                                        <button type="submit" class="btn btn-block btn-primary">Save</button>                                        
                                    </form>
                                    <br>
                                    <form>
                                        <a href="{{url()->previous()}}" class="btn btn-block btn-success">Cancel</a>
                                    </form>
                                </div>
                              </div> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    <!-- END MAIN CONTENT-->
</div>
<!-- END PAGE CONTAINER-->

@include('includes.footer')
</body>

</html>
<!-- end document-->